@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="d-flex flex-column align-items-center ms-auto me-auto w-75 mt-4">
        <div class="text-center mb-4">
            <h2>Reports</h2>
            <p>Use this page to manage reports.</p>
        </div>
        
        @if($reports->count() > 0)
        <form method="GET" class="text-center mb-4">
            <div class="d-flex flex-row">
                <div class="mb-3 p-2">
                    <label for="filterType" class="form-label">Filter Type:</label>
                    <select name="filterType" id="filterType" class="form-select">
                        <option value="all" {{ request('filterType') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="user" {{ request('filterType') == 'user' ? 'selected' : '' }}>User Reports</option>
                        <option value="post" {{ request('filterType') == 'post' ? 'selected' : '' }}>Post Reports</option>
                    </select>
                </div>
                <div class="mb-3 p-2">
                    <label for="filterReason" class="form-label">Filter Reason:</label>
                    <select name="filterReason" id="filterReason" class="form-select">
                        <option value="all" {{ request('filterReason') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="harassment" {{ request('filterReason') == 'harassment' ? 'selected' : '' }}>Harassment</option>
                        <option value="spam" {{ request('filterReason') == 'spam' ? 'selected' : '' }}>Spam</option>
                        <option value="inappropriate_content" {{ request('filterReason') == 'inappropriate_content' ? 'selected' : '' }}>Inappropriate Content</option>
                        <option value="self_harm" {{ request('filterReason') == 'self_hard' ? 'selected' : '' }}>Self Harm</option>
                        <option value="hate_speech" {{ request('filterReason') == 'hate_speech' ? 'selected' : '' }}>Hate Speech</option>
                    </select>
                </div>
            </div>
            <button type="submit" id="applyFiltersBtn" class="btn btn-primary">Apply Filters</button>
        </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>User/Post</th>
                            <th>Source</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Actions</th>
                            <th>Handle Report</th>
                        </tr>
                    </thead>
                    <tbody id="reportsTable">
                        @include('partials.reports_table', ['reports' => $reports])
                    </tbody>
                </table>
            </div>
        @else
            <p>No reports available.</p>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function applyFilters() {
                var filterType = document.getElementById('filterType').value;
                var filterReason = document.getElementById('filterReason').value;
    
                // Create a new XMLHttpRequest object
                var xhr = new XMLHttpRequest();
    
                // Configure the request
                xhr.open('GET', '{{ route("admin.reports") }}' + '?type=' + encodeURIComponent(filterType) + '&reason=' + encodeURIComponent(filterReason), true);
    
                // Define the onload and onerror handlers
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if(xhr.responseText != '') {
                            var table_head = "<table class='table table-bordered table-hover'><thead class='table-dark'>"
                            var table_content = "<tr><th>User/Post</th><th>Source</th><th>Date</th><th>Reason</th><th>Actions</th><th>Handle Report</th></tr></thead><tbody id='reportsTable'>";
                            var table_end = "</tbody></table>";
                            document.querySelector('.table-responsive').innerHTML = table_head + table_content + xhr.responseText + table_end;
                        } else {
                            document.querySelector('.table-responsive').innerHTML = '<p>No reports found that match the selected filters.</p>';
                        }
                    } else {
                        // Error: log the error
                        console.error('Error:', xhr.status, xhr.statusText);
                    }
                };
    
                xhr.onerror = function () {
                    console.error('Network error');
                };
    
                // Send the request
                xhr.send();
            }
    
            // Submit the form on button click
            document.getElementById('applyFiltersBtn').addEventListener('click', function (e) {
                e.preventDefault();
                applyFilters();
            });
        });
    </script>    
@endsection