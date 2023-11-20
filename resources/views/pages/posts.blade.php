@extends('layouts.app')

@section('title', 'Posts')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="flex-grow-1" style="margin-left: 280px;">
    <div class="overflow-auto h-100">
        <section id="posts" class="container my-4 d-flex justify-content-center">
            <div>
                @each('partials.post', $posts, 'post')
            </div>
        </section>
    </div>
</div>
@endsection
