@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center ms-auto me-auto w-100 overflow-auto">
    @isset($post)
        <h2 class="mt-4">Reporting Post from user: 
            <a href="{{ route('user.profile', ['id' => $post->user->user_id]) }}" class="link-primary:hover">{{$post->user->name}}</a>
        </h2> 
        <div class="card w-50 mt-4">
            <div class="card-body">
                <h5 class="card-title">Report Post</h5>
                <p class="card-text">Please select the reason for reporting the post.</p>

                <form action="{{ route('post.submitReport', ['id' => $post->post_id]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="report_type" class="form-label">Report Type:</label>
                        <select class="form-select" id="report_type" name="report_type" required>
                            <option value="harassment">Harassment</option>
                            <option value="hate_speech">Hate Speech</option>
                            <option value="inappropriate_content">Inappropriate Content</option>
                            <option value="spam">Spam</option>
                            <option value="self_harm">Self Harm</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </form>
            </div>
        </div>
    @endisset

    @isset($user)
        <h2 class="mt-4">Reporting User: <a href="{{ route('user.profile', ['id' => $user->user_id]) }}" class="link-primary:hover">{{$user->name}}</a></h2>
        <div class="card w-50 mt-4">
            <div class="card-body">
                <h5 class="card-title">Report User</h5>
                <p class="card-text">Please select the reason for reporting the user.</p>

                <form action="{{ route('user.submitReport', ['id' => $user->user_id]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="report_type" class="form-label">Report Type:</label>
                        <select class="form-select" id="report_type" name="report_type" required>
                            <option value="harassment">Harassment</option>
                            <option value="hate_speech">Hate Speech</option>
                            <option value="inappropriate_content">Inappropriate Content</option>
                            <option value="spam">Spam</option>
                            <option value="self_harm">Self Harm</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </form>
            </div>
        </div>
    @endisset
</div>
@endsection
