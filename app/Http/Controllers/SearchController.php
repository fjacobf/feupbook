<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function show()
    {
        $users = User::all();

        return view('pages.search', compact('users'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $users = User::where('username', 'ILIKE', "%$query%")->get();

        return view('partials.search-results', compact('users'));
    }
}

