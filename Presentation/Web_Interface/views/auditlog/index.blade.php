@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
    <div class="page-header">
        <div>
            <span class="eyebrow">Module 4 — Internal Review</span>
            <h1>Audit Log</h1>
        </div>
        <a href="{{ route('auditlog.export') }}" class="btn btn-ghost">Export PDF</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Staff</th>
                    <th>Action</th>
                    <th>Affected Record</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($log->Timestamp)->format('d M Y, h:i A') }}</td>
                        <td>{{ $log->staff->Username ?? 'System' }}</td>
                        <td>{{ $log->Action }}</td>
                        <td class="code">{{ $log->Affected_Record }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="4">No audit entries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $logs->links() }}</div>
@endsection
