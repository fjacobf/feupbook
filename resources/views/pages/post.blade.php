@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <section class="d-flex justify-content-center" style="margin-left: 280px;">
        @include('partials.post', ['post' => $post])
    </section>
@endsection