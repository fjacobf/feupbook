<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $users = User::where('username', 'ILIKE', "%$query%")->get();

        if ($request->ajax()) {
            // If it's an AJAX request, return only the search results as HTML
            return view('partials.search-results', compact('users', 'query'))->render();
        } else {
            // If it's a regular request, return the full search view
            return view('pages.search', compact('users', 'query'));
        }
    }
}

