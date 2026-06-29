<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $invoices = Invoice::with(['supplier', 'deliveryOrder', 'staff'])
            ->when($status, fn ($q) => $q->where('Status', $status))
            ->orderByDesc('Created_At')
            ->get();

        return view('invoice.index', [
            'invoices' => $invoices,
            'statuses' => array_merge(Invoice::STAGES, ['Rejected']),
            'activeStatus' => $status,
        ]);
    }

    public function show(string $id)
    {
        $invoice = Invoice::with(['supplier', 'deliveryOrder', 'staff'])->findOrFail($id);
        $financeOfficers = Staff::where('Role', 'Finance Officer')->where('Status', 'Active')->get();

        return view('invoice.show', [
            'invoice' => $invoice,
            'financeOfficers' => $financeOfficers,
        ]);
    }

    public function forwardToFinance(Request $request, string $id)
    {
        $data = $request->validate([
            'staff_id' => 'required|exists:ktm staff,User_ID',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->Staff_id = $data['staff_id'];
        $invoice->Status = 'Finance Review';
        $invoice->save();

        AuditLog::record(
            "Forwarded Invoice {$invoice->Invoice_num} to Finance (Staff #{$data['staff_id']})",
            $invoice->Invoice_num
        );

        NotificationLog::create([
            'User_ID' => $data['staff_id'],
            'Supplier_id' => $invoice->supplier_ID,
            'Type' => 'System',
            'Content' => "Invoice {$invoice->Invoice_num} has been forwarded to you for finance review.",
            'Status' => 'Sent',
            'Creates_At' => now(),
        ]);

        return back()->with('success', 'Invoice forwarded to Finance.');
    }

    public function updateStatus(Request $request, string $id)
    {
        $data = $request->validate([
            'status' => 'required|in:Finance Review,Payment Processing,Paid',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->Status = $data['status'];
        $invoice->save();

        AuditLog::record(
            "Updated Invoice {$invoice->Invoice_num} status to {$data['status']}",
            $invoice->Invoice_num
        );

        NotificationLog::create([
            'User_ID' => session('staff_id'),
            'Supplier_id' => $invoice->supplier_ID,
            'Type' => 'Email',
            'Content' => "Invoice {$invoice->Invoice_num} status changed to {$data['status']}.",
            'Status' => 'Sent',
            'Creates_At' => now(),
        ]);

        return redirect()->route('invoice.show', $id)->with('success', 'Invoice status updated.');
    }

    public function reject(Request $request, string $id)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:250',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->Status = 'Rejected';
        $invoice->Reason = $data['reason'];
        $invoice->save();

        AuditLog::record(
            "Rejected Invoice {$invoice->Invoice_num}: {$data['reason']}",
            $invoice->Invoice_num
        );

        NotificationLog::create([
            'User_ID' => session('staff_id'),
            'Supplier_id' => $invoice->supplier_ID,
            'Type' => 'Email',
            'Content' => "Invoice {$invoice->Invoice_num} has been rejected: {$data['reason']}",
            'Status' => 'Sent',
            'Creates_At' => now(),
        ]);

        return redirect()->route('invoice.show', $id)->with('success', 'Invoice rejected.');
    }

        public function exportPdf(string $id)
        {
            $invoice = Invoice::with(['supplier', 'deliveryOrder', 'staff'])->findOrFail($id);
    
            $pdf = Pdf::loadView('invoice.pdf', ['invoice' => $invoice])->setPaper('a4');
            return $pdf->download("Invoice_{$invoice->Invoice_num}.pdf");
        }
}
