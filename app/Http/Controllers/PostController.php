<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;

class PostController extends Controller
{
   /**
     * Show the post for a given id.
     */
    public function show(string $id): View
    {
        // Get the card.
        $post = Post::findOrFail($id); 

        // Use the pages.card template to display the card.
        return view('pages.post', [
            'post' => $post
        ]);
    }

    /**
     * Show all the posts available in the database.
     */
    public function list()
    {
      //check if user is logged in
      if (Auth::check()) {
        //user is logged in
        $posts = Post::with('user')->orderBy('date', 'desc')->paginate(10);
        return view('pages.posts', ['posts' => $posts]);
      } else {
        //user is not logged in
        return redirect('/');
      }
    }

    /**
     * Creates a new card.
     */
    // public function create(Request $request)
    // {
    //     // Create a blank new Card.
    //     $card = new Card();

    //     // Check if the current user is authorized to create this card.
    //     $this->authorize('create', $card);

    //     // Set card details.
    //     $card->name = $request->input('name');
    //     $card->user_id = Auth::user()->id;

    //     // Save the card and return it as JSON.
    //     $card->save();
    //     return response()->json($card);
    // }

    /**
     * Delete a card.
     */
    // public function delete(Request $request, $id)
    // {
    //     // Find the card.
    //     $card = Card::find($id);

    //     // Check if the current user is authorized to delete this card.
    //     $this->authorize('delete', $card);

    //     // Delete the card and return it as JSON.
    //     $card->delete();
    //     return response()->json($card);
    // }
}
