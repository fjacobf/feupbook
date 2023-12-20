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
      try{
        $comment = Comment::findorFail($id);

        $this->authorize('update', $comment);
        
        $authUser = Auth::user();

        $commentLike = new CommentLike([
                'user_id' => $authUser->user_id,
                'comment_id' => $comment->comment_id
            ]);

          $commentLike->save();

          return response()->json(['status' => 200]);
        
      }
      catch(AuthorizationException $e){
        return response()->json(['error' => 'You are not authorized to like this comment'], 403);
      }
  }

    public function dislike($id) {
      try{
        $comment = Comment::findorFail($id);

        $this->authorize('update', $comment);

        $authUser = Auth::user();

        CommentLike::where('user_id', $authUser->user_id)->where('comment_id', $comment->comment_id)->delete();
        
        return response()->json(['status' => 200]);
      }
      catch(AuthorizationException $e){
        return response()->json(['error' => 'You are not authorized to dislike this comment'], 403);
      }
  }
}
