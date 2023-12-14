@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center ms-auto me-auto w-100">
    <div class="d-flex justify-content-center">
        <h3 class="bg-primary text-white rounded mt-3 p-2">Edit Post</h3>
    </div>
    <form class="container-lg d-flex flex-column align-items-center w-100" enctype="multipart/form-data" action="{{ route('post.update', ['id' => $post->post_id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="d-flex flex-column justify-content-start w-50">
            <label for="content" class="form-label mb-3 h5">Post Content</label>
            <textarea class="form-control w-100 mb-3" id="content" name="content" rows="3">{{ $post->content }}</textarea>
            @if ($post->image)
                <div class="mb-3">
                    <img src="{{ asset($post->image) }}" alt="Current Image" class="mb-3 rounded mx-auto d-block w-50">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage">
                        <label class="form-check-label" for="removeImage">
                            Remove current image
                        </label>
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <label for="image" class="form-label">Upload new Image</label>
                <input type="file" class="form-control" name="image" id="image">
            </div>
            <div class="w-25">
                <button type="submit" class="btn btn-primary btn-sm mt-3">
                    <p class="h5 mb-1">Update Post</p>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
