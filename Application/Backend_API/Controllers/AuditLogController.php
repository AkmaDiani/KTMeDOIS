<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = \App\Models\AuditLog::with('staff')
            ->orderByDesc('Timestamp')
            ->paginate(20);

        return view('auditlog.index', ['logs' => $logs]);
    }

    public function exportPdf()
    {
        $logs = \App\Models\AuditLog::with('staff')
            ->orderByDesc('Timestamp')
            ->get();

        $pdf = Pdf::loadView('auditlog.pdf', ['logs' => $logs])->setPaper('a4');
        return $pdf->download("Audit_Log.pdf");
    }
}
