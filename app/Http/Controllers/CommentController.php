<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request)
    {
      $validatedData = $request->validate([
          'content' => 'required|max:1000',
      ]);

      $comment = new Comment;
      $comment->content = $validatedData['content'];
      $comment->author_id = Auth::id(); // Set the owner_id to the current user's ID
      $comment->post_id = $request->post_id;
      $comment->previous = $request->comment_id;
      $comment->save();

      return redirect()->back()->with('success', 'Comment created successfully!');
    }

    public function delete($id)
    {
      $comment = Comment::find($id);

      if ($comment) {
          // Delete replies to the comment first
          Comment::where('previous', $id)->delete();
          // Delete the comment itself
          $comment->delete();
      }

      return redirect()->back()->with('success', 'Comment deleted successfully!');
    }

}
