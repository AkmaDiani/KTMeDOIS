<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Helvetica", Arial, sans-serif; color: #1B2430; font-size: 10.5px; }
        table.log { width: 100%; border-collapse: collapse; }
        table.log th { background: #0E2A47; color: #fff; padding: 7px 9px; text-align: left; font-size: 9.5px; text-transform: uppercase; }
        table.log td { border-bottom: 1px solid #DDE3EA; padding: 7px 9px; }
        table.log tr:nth-child(even) td { background: #FAFBFC; }
        .footer-note { margin-top: 24px; font-size: 9px; color: #8A93A1; border-top: 1px solid #DDE3EA; padding-top: 8px; }
        .meta { font-size: 10px; color: #44546B; margin-bottom: 14px; }
    </style>
</head>
<body>

    @include('pdf.letterhead', ['reportTitle' => 'Audit Log Report'])

    <div class="meta">Total entries: {{ $logs->count() }}</div>

    <table class="log">
        <thead>
            <tr>
                <th style="width:130px;">Timestamp</th>
                <th style="width:120px;">Staff</th>
                <th>Action</th>
                <th style="width:110px;">Affected Record</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->Timestamp)->format('d M Y, h:i A') }}</td>
                    <td>{{ $log->staff->Username ?? 'System' }}</td>
                    <td>{{ $log->Action }}</td>
                    <td>{{ $log->Affected_Record }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-note">
        This document is a system-generated audit trail export from KTM eDOIS, intended for compliance and review reference.
    </div>

</body>
</html>
