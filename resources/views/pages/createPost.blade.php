@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="col-9">
    <div class="d-flex justify-content-center">
        <h3 class="bg-primary text-white rounded mt-3 p-2">Create Post</h3>
    </div>

    <form class="container-lg d-flex flex-column align-items-center w-100" enctype="multipart/form-data" action="{{ route('post.store') }}" method="POST">
        @csrf
        <div class="d-flex flex-column justify-content-start w-50">
            <label for="content" class="form-label mb-3 h5">Post Content</label>
            <textarea class="form-control w-100" id="content" name="content" rows="3"></textarea>
            <input type="file" class="form-control w-100 mt-3" id="image" name="image">
            <div class="w-25">
                <button type="submit" class="btn btn-primary btn-sm mt-3">
                    <p class="h5 mb-1"> Submit</p>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
