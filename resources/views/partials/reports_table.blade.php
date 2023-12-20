@foreach($reports as $report)
    <tr>
        <td>
            @if($report->user_id != null) User
            @elseif($report->post_id != null) Post
            @endif
        </td>
        <td>
            @if($report->user_id != null)
                <a href="{{ route('user.profile', ['id' => $report->user_id]) }}">
                    {{ \App\Models\User::find($report->user_id)->name }}
                </a>
            @elseif($report->post_id != null)
                <a href="{{ route('post.show', ['id' => $report->post_id]) }}">Post ID: {{ $report->post_id }}</a>
            @endif
        </td>
        <td>{{ $report->date->format('Y-m-d') }}</td>
        <td>{{ ucfirst(str_replace('_', ' ', $report->report_type)) }}</td>
        <td>
            @if($report->user_id != null)
                @if(\App\Models\User::find($report->user_id)->user_type === 'suspended') User is already suspended.
                @else
                <form action="{{ route('admin.suspendUser', ['id' => $report->user_id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-warning">Suspend User</button>
                </form>
                @endif
            @endif
        </td>
        <td>
            <form action="{{ route('admin.deleteReport', ['id' => $report->report_id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-info">Delete Report</button>
            </form>
        </td>
    </tr>
@endforeach