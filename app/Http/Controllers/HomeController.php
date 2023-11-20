<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;

class HomeController extends Controller
{
    public function index()
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
}
