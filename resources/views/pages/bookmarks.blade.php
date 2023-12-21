@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center ms-auto me-auto w-100 overflow-auto">
   <div class="d-flex justify-content-center">
      <h3 class="bg-primary text-white rounded mt-3 p-2">Bookmarks</h3>
   </div>
   <div class="container-lg d-flex justify-content-center align-items-center w-100">
      <ul class="list-unstyled mb-4 w-100" style="max-width: 800px">
            @forelse($posts as $post)
               @include('partials.post', ['post' => $post])
            @empty
            <div class="alert alert-info mt-4" role="alert">
               <h4 class="alert-heading">No posts to display!</h4>
               <p>When the user adds posts to the bookmarks, those posts will be displayed here.</p>
            </div>
         @endforelse
      </ul>
   </div>
</div>
@endsection
