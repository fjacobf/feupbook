<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Post;

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
      // Check if user is logged in
      if (Auth::check()) {
          // User is logged in
          // Check if the user is an admin
          if (auth()->user()->user_type == 'admin') {
              // For admin users, get all posts including private ones
              $posts = Post::whereHas('user', function($query) {
                $query->where('owner_id', '!=', Auth::id());
            })->with('comments')
              ->orderBy('created_at', 'desc')->paginate(10);
          } else {
              // For non-admin users, get posts only from public users excluding the logged-in user
              $posts = Post::whereHas('user', function($query) {
                  $query->where('private', false)
                        ->where('owner_id', '!=', Auth::id());
              })->with('comments')
                ->orderBy('created_at', 'desc')->paginate(10);
          }

          return view('pages.posts', ['posts' => $posts]);
      } else {
          // User is not logged in
          return redirect('/');
      }
    }



    public function forYou()
    {
        // Check if user is logged in
      if (Auth::check()) {
        // User is logged in
        $user = Auth::user();

        // Get the IDs of users that the current user follows
        $followingIds = $user->following()->pluck('users.user_id')->toArray();
        
        // Fetch posts only from these users
        $posts = Post::with('user')
                    ->whereIn('owner_id', $followingIds)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('pages.posts', ['posts' => $posts]);
      } 
      else {
          // User is not logged in
          return redirect('/');
        }
    }

    public function create()
    {
        return view('pages.createPost');
    }

    public function store(Request $request)
    {
      $validatedData = $request->validate([
          'content' => 'required|max:1000',
      ]);

      $post = new Post;
      $post->content = $validatedData['content'];
      $post->owner_id = Auth::id(); // Set the owner_id to the current user's ID
      $post->save();

      return redirect('/home')->with('success', 'Post created successfully!');
    }

    public function edit(string $id)
    {
      $post = Post::findOrFail($id);

      // Unauthorized action check
      if (Auth::id() !== $post->owner_id) {
          abort(403, 'Unauthorized action.');
      }

      return view('pages.editPost', ['post' => $post]);
    }


    public function update(Request $request, string $id)
    {
      // Validate the request
      $validatedData = $request->validate([
          'content' => 'required|max:1000', // Validation rules for the content
      ]);

      // Find the post
      $post = Post::findOrFail($id);

      // Check if the authenticated user is the owner of the post
      if (Auth::id() !== $post->owner_id) {
          abort(403, 'Unauthorized action.');
      }

      // Update the post
      $post->content = $validatedData['content'];
      $post->save();

      // Redirect with a success message
      return redirect()->route('user.profile', ['id' => Auth::id()])->with('success', 'Post updated successfully!');
    }

    public function delete(string $id)
    {
      $post = Post::findOrFail($id);

      // Check if the authenticated user is the owner of the post
      if (Auth::id() !== $post->owner_id) {
          abort(403, 'Unauthorized action.');
      }

      // Delete the post
      $post->delete();

      // Redirect with a success message
      return redirect()->route('user.profile', ['id' => Auth::id()])->with('success', 'Post deleted successfully!');
    }

}
