<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Comment;
use App\Models\Post;
use App\Models\CommentLike;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store(Request $request)
    {
      try
      {  
        $validatedData = $request->validate([
            'content' => 'required|max:1000',
        ]);

        $post = Post::findorFail($request->post_id);

        $this->authorize('create', [Comment::class, $post]);

        $comment = new Comment;
        $comment->content = $validatedData['content'];
        $comment->author_id = Auth::id(); 
        $comment->post_id = $request->post_id;
        $comment->previous = $request->comment_id;
        $comment->save();

        return redirect()->back()->with('success', 'Comment created successfully!');
      }
      catch(AuthorizationException $e){
        return redirect()->back()->withErrors(['message' => 'You are not authorized to comment on this post']);
      }
    }

    public function delete(Request $request, $id)
    {
      try
      {  
        $comment = Comment::findOrFail($id);
        
        Log::info($comment->comment_id);

        $this->authorize('delete', $comment);

        if ($comment) {
            // Delete replies to the comment first
            Comment::where('previous', $id)->delete();
            // Delete the comment itself
            $comment->delete();
        }

        return redirect()->back()->with('success', 'Comment deleted successfully!');
      }
      catch(AuthorizationException $e){
        return redirect()->back()->withErrors(['message' => 'You are not authorized to delete this comment']);
      }
    }

    public function like($id) {
      $authUser = Auth::user();
      $comment = Comment::find($id);

      $commentLike = new CommentLike([
              'user_id' => $authUser->user_id,
              'comment_id' => $comment->comment_id
          ]);

          $commentLike->save();
      
      return redirect()->back();
  }

    public function dislike($id) {
      $authUser = Auth::user();
      $comment = Comment::find($id);
      $commentlike = CommentLike::where('user_id', $authUser->user_id)->where('comment_id', $comment->comment_id)->delete();

      return redirect()->back();
  }
}
