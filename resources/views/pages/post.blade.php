@extends('layouts.app')

@section('content')
    <section id="posts">
        @include('partials.post', ['post' => $post])
    </section>
@endsection