<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Post;

use Illuminate\Support\Facades\Log;

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
                        ->where('owner_id', '!=', Auth::id());
              })->with('comments')
                ->orderBy('created_at', 'desc')->paginate(10);
          }

          return view('pages.posts', ['posts' => $posts]);
      } catch (AuthorizationException $e) {
          return redirect('/');
      }
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

        return view('pages.posts', ['posts' => $posts]);
      } 
      catch(AuthorizationException $e){
        return redirect('/')->withErrors(['message' => 'Log in in order to see posts']);
      }
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
            'content' => 'required|max:1000',
        ]);

        $this->authorize('create', Post::class);

        $post = new Post;
        $post->content = $validatedData['content'];
        $post->owner_id = Auth::id(); // Set the owner_id to the current user's ID
        $post->save();

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
            'content' => 'required|max:1000', 
        ]);

        $post = Post::findOrFail($id);

        $this->authorize('update', $post);

        $post->content = $validatedData['content'];
        $post->save();

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

}
