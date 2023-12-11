@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="flex-grow-1" style="margin-left: 280px;">
    <div class="d-flex justify-content-center">
        <h3 class="bg-primary text-white rounded mt-3 p-2">Edit Post</h3>
    </div>
    <form class="container-lg d-flex flex-column align-items-center w-100" action="{{ route('post.update', ['id' => $post->post_id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="d-flex flex-column justify-content-start w-50">
            <label for="content" class="form-label mb-3 h5">Post Content</label>
            <textarea class="form-control w-100" id="content" name="content" rows="3">{{ $post->content }}</textarea>
            <div class="w-25">
                <button type="submit" class="btn btn-primary btn-sm mt-3">
                    <p class="h5 mb-1">Update Post</p>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
