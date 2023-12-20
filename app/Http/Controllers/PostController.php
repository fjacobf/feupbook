<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\User;
use App\Models\Mention;
use App\Models\Report;

class PostController extends Controller
{
   /**
     * Show the post for a given id.
     */
    public function show(string $id)
    {
      try {
          $post = Post::with('user')->findOrFail($id);

          $this->authorize('view', $post);

          return view('pages.post', ['post' => $post]);
      } catch (AuthorizationException $e) {
          return redirect()->back()->withErrors(['message' => 'You are not authorized to view this post']);
      }
    }

    /**
     * Show the posts of public users. Show posts of public and private users if you are an admin.
     */
    public function list()
    {
      try {
          $this->authorize('viewAny', Post::class);

          $user = auth()->user();

          if ($user->user_type == 'admin') {
              $posts = Post::whereHas('user', function($query) {
                  $query->where('owner_id', '!=', Auth::id());
              })->with('comments')
                ->orderBy('created_at', 'desc')->paginate(10);
          } else {
              $posts = Post::whereHas('user', function($query) use ($user) {
                  $query->where('private', false)
                        ->where('user_type', '!=', 'suspended')
                        ->where('owner_id', '!=', Auth::id());
              })->with('comments')
                ->orderBy('created_at', 'desc')->paginate(10);
          }

          return view('pages.posts', ['posts' => $posts,'pageContext' => 'home']);
      } catch (AuthorizationException $e) {
          return redirect('/');
      }
    }
    
    public function loadFeedPosts()
    {
      $user = auth()->user();

      if ($user->user_type == 'admin') {
        $posts = Post::whereHas('user', function($query) {
          $query->where('owner_id', '!=', Auth::id());
        })->with('comments')
          ->orderBy('created_at', 'desc')->paginate(10);
      } else {
        $posts = Post::whereHas('user', function($query) use ($user) {
          $query->where('private', false)
              ->where('owner_id', '!=', Auth::id());
        })->with('comments')
          ->orderBy('created_at', 'desc')->paginate(10);
      }

      $renderedPosts = $posts->map(function ($post) {
        return view('partials.post', ['post' => $post])->render();
      });

      return response()->json([
        'posts' => $renderedPosts,
        'next_page_url' => $posts->nextPageUrl()
      ]);
    }

    public function forYou()
    {
      try{
        $this->authorize('viewAny', Post::class);

        $user = Auth::user();

        $followingIds = $user->following()->pluck('users.user_id')->toArray();
        
        $posts = Post::with('user')
                    ->whereIn('owner_id', $followingIds)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('pages.posts', ['posts' => $posts,'pageContext' => 'forYou']);
      } 
      catch(AuthorizationException $e){
        return redirect('/')->withErrors(['message' => 'Log in in order to see posts']);
      }
    }

    public function loadForYouPosts()
    {
      $user = Auth::user();

      $followingIds = $user->following()->pluck('users.user_id')->toArray();

      $posts = Post::with('user')
            ->whereIn('owner_id', $followingIds)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

      $renderedPosts = $posts->map(function ($post) {
        return view('partials.post', ['post' => $post])->render();
      });

      return response()->json([
        'posts' => $renderedPosts,
        'next_page_url' => $posts->nextPageUrl()
      ]);
    }

    public function create()
    {
      try
      {  
        $this->authorize('create', Post::class);

        return view('pages.createPost');
      }
      catch(AuthorizationException $e){
        return redirect('/')->withErrors(['message' => 'Log in in order to create a post']);
      }
    }

    public function store(Request $request)
    {
      try
      {  
        $validatedData = $request->validate([
            'content' => 'required|max:2000',
            'image' => 'file|mimes:jpeg,png,jpg,jpe,gif,svg|max:5048',
        ]);

        $this->authorize('create', Post::class);

        $post = new Post;
        $post->content = $validatedData['content'];
        $post->owner_id = Auth::id();

        if ($request->hasFile('image')) {
          $imageName = time() . '.' . $request->image->extension();
          $request->image->move(public_path('images'), $imageName);
          $post->image = 'images/' . $imageName;
        }

        $post->save();

        preg_match_all('/@(\w+)/', $post->content, $matches);
        $usernames = $matches[1];

        foreach ($usernames as $username) {
            $mentionedUser = User::where('username', $username)->first();
            if ($mentionedUser) {
                Mention::create([
                    'post_id' => $post->post_id,
                    'user_mentioned' => $mentionedUser->user_id
                ]);
            }
        }

        return redirect()->route('user.profile', ['id' => Auth::id()])->with('success', 'Post created successfully!');
      }
      catch(AuthorizationException $e){
        return redirect('/')->withErrors(['message' => 'Log in in order to create a post']);
      }
    }

    public function edit(string $id)
    {
      try{
        $post = Post::findOrFail($id);

        $this->authorize('update', $post);
        
        return view('pages.editPost', ['post' => $post]);
      }
      catch(AuthorizationException $e){
        return redirect()->back()->withErrors(['message' => 'You are not authorized to edit this post']);
      }
    }


    public function update(Request $request, string $id)
    {
      try
      {
        $validatedData = $request->validate([
            'content' => 'required|max:2000', 
        ]);

        $post = Post::findOrFail($id);

        $this->authorize('update', $post);

        $post->content = $validatedData['content'];

        if ($request->has('remove_image') && $post->image) {
          if (file_exists(public_path($post->image))) {
              unlink(public_path($post->image));
          }
          $post->image = null;
        }
  
        if ($request->hasFile('image')) {
          $imageName = time().'.'.$request->image->extension();  
          $request->image->move(public_path('images'), $imageName);
          $post->image = 'images/'.$imageName;
        }

        $post->save();

        preg_match_all('/@(\w+)/', $post->content, $matches);
        $usernames = $matches[1];

        $post->mentions()->delete();

        foreach ($usernames as $username) {
            $mentionedUser = User::where('username', $username)->first();
            if ($mentionedUser) {
                // Create or update the Mention
                Mention::updateOrCreate(
                    ['post_id' => $post->post_id, 'user_mentioned' => $mentionedUser->user_id],
                    ['post_id' => $post->post_id, 'user_mentioned' => $mentionedUser->user_id]
                );
            }
        }

        return redirect()->route('user.profile', ['id' => Auth::id()])->with('success', 'Post updated successfully!');
      }
      catch(AuthorizationException $e){
        return redirect('/home')->withErrors(['message' => 'You are not authorized to edit this post']);
      }
    }

    public function delete(string $id)
    {
      try
      {  
        $post = Post::findOrFail($id);

        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('user.profile', ['id' => Auth::id()])->with('success', 'Post deleted successfully!');
      }
      catch(AuthorizationException $e){
        return redirect()->back()->withErrors(['message' => 'You are not authorized to delete this post']);
      }
    }

    public function like($id){
      try{
        $post = Post::findOrFail($id);

        $this->authorize('like', $post);

        $post->likes()->create([
          'user_id' => Auth::id(),
          'post_id' => $post->post_id,
        ]);

        return response()->json(['status' => 200]);
      }
      catch(AuthorizationException $e){
        return response()->json(['error' => 'You are not authorized to like this post'], 403);
      }
    }

    public function dislike($id){
      try{
        $post = Post::findOrFail($id);

        $this->authorize('like', $post);

        $post->likes()->where('user_id', Auth::id())->where('post_id', $post->post_id)->delete();
        
        return response()->json(['status' => 200]);
      }
      catch(AuthorizationException $e){
        return response()->json(['error' => 'You are not authorized to dislike this post'], 403);
      }
    }

    public function listBookmarks()
    {
      try {
        $this->authorize('viewAny', Post::class);

        $user = auth()->user();

        $posts = Post::whereHas('bookmarks', function($query) use ($user) {
          $query->where('user_id', $user->user_id);
        })->with('comments')
          ->orderBy('created_at', 'desc')->paginate(10);

        return view('pages.bookmarks', ['posts' => $posts]);
      } catch (AuthorizationException $e) {
        return redirect('/');
      }
    }

    public function bookmark($id){
      try{
        $post = Post::findOrFail($id);

        $this->authorize('bookmark', $post);

        $post->bookmarks()->create([
          'user_id' => Auth::id(),
          'bookmarked_post' => $post->post_id,
        ]);

        return response()->json(['status' => 200, 'message' => 'Post bookmarked successfully!']);
      }
      catch(AuthorizationException $e){
        return response()->json(['error' => 'You are not authorized to bookmark this post'], 403);
      }
    }

    public function unbookmark($id){
      try{
        $post = Post::findOrFail($id);

        $this->authorize('bookmark', $post);

        $post->bookmarks()->where('user_id', Auth::id())->where('bookmarked_post', $post->post_id)->delete();

        return response()->json(['status' => 200, 'message' => 'Post unbookmarked successfully!']);
      }
      catch(AuthorizationException $e){
        return response()->json(['error' => 'You are not authorized to unbookmark this post'], 403);
      }
    }

    public function showReportForm($post_id) {
      try {
        $post = Post::findOrFail($post_id);

        $this->authorize('report', $post);

        return view('pages.report', ['post' => $post]);
      } catch (AuthorizationException $e) {
        return redirect()->back()->withErrors(['message' => 'You are not authorized to report this post']);
      }
    }

    public function submitReport(Request $request, $post_id) {
      try {
        $post = Post::findOrFail($post_id);

        $this->authorize('report', $post);

        $validatedData = $request->validate([
          'report_type' => 'required',
        ]);

        Report::create([
          'post_id' => $post->post_id,
          'date' => now(),
          'report_type' => $validatedData['report_type'],
        ]);

        return redirect()->route('post.show', ['id' => $post->post_id])->with('success', 'Post reported successfully!');
      } catch (AuthorizationException $e) {
        return redirect()->back()->withErrors(['message' => 'You are not authorized to report this post']);
      }
    }
}
