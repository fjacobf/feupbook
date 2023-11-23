<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $users = User::where('username', 'LIKE', "%$query%")->get();

        if ($request->ajax()) {
            // If it's an AJAX request, return only the search results as a partial view
            return View::make('partials.search-results', compact('users', 'query'));
        } else {
            // If it's a regular request, return the full search view
            return view('pages.search', compact('users', 'query'));
        }
    }
}

