<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DeliveryOrder;
use App\Models\NotificationLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryOrderReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $deliveryOrders = DeliveryOrder::with(['supplier', 'staff'])
            ->when($status, fn ($q) => $q->where('Status', $status))
            ->orderByDesc('created_date')
            ->get();

        return view('do.index', [
            'deliveryOrders' => $deliveryOrders,
            'statuses' => array_merge(DeliveryOrder::STAGES, ['Rejected']),
            'activeStatus' => $status,
        ]);
    }

    public function show(string $id)
    {
        $deliveryOrder = DeliveryOrder::with(['supplier', 'staff', 'items', 'invoices'])->findOrFail($id);
        $officers = Staff::where('Role', 'KTM Officer')->where('Status', 'Active')->get();

        return view('do.show', [
            'deliveryOrder' => $deliveryOrder,
            'officers' => $officers,
        ]);
    }

    public function assignReviewer(Request $request, string $id)
    {
        $data = $request->validate([
            'staff_id' => 'required|exists:ktm staff,User_ID',
        ]);

        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $deliveryOrder->Staff_id = $data['staff_id'];
        if ($deliveryOrder->Status === 'Submitted') {
            $deliveryOrder->Status = 'Under Review';
        }
        $deliveryOrder->save();

        AuditLog::record(
            "Assigned Delivery Order {$deliveryOrder->DO_number} to reviewer (Staff #{$data['staff_id']})",
            $deliveryOrder->DO_number
        );

        NotificationLog::create([
            'User_ID' => $data['staff_id'],
            'Supplier_id' => null,
            'Type' => 'System',
            'Content' => "You have been assigned to review Delivery Order {$deliveryOrder->DO_number}.",
            'Status' => 'Sent',
            'Creates_At' => now(),
        ]);

        return back()->with('success', 'Reviewer assigned successfully.');
    }

    public function approve(string $id)
    {
        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $deliveryOrder->Status = 'Approved';
        $deliveryOrder->Reason = '-';
        $deliveryOrder->save();

        AuditLog::record("Approved Delivery Order {$deliveryOrder->DO_number}", $deliveryOrder->DO_number);

        NotificationLog::create([
            'User_ID' => session('staff_id'),
            'Supplier_id' => $deliveryOrder->supplier_ID,
            'Type' => 'Email',
            'Content' => "Delivery Order {$deliveryOrder->DO_number} has been approved.",
            'Status' => 'Sent',
            'Creates_At' => now(),
        ]);

        return redirect()->route('do.show', $id)->with('success', 'Delivery Order approved.');
    }

    public function reject(Request $request, string $id)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:250',
        ]);

        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $deliveryOrder->Status = 'Rejected';
        $deliveryOrder->Reason = $data['reason'];
        $deliveryOrder->save();

        AuditLog::record(
            "Rejected Delivery Order {$deliveryOrder->DO_number}: {$data['reason']}",
            $deliveryOrder->DO_number
        );

        NotificationLog::create([
            'User_ID' => session('staff_id'),
            'Supplier_id' => $deliveryOrder->supplier_ID,
            'Type' => 'Email',
            'Content' => "Delivery Order {$deliveryOrder->DO_number} has been rejected: {$data['reason']}",
            'Status' => 'Sent',
            'Creates_At' => now(),
        ]);

        return redirect()->route('do.show', $id)->with('success', 'Delivery Order rejected.');
    }
     
    public function exportPdf(string $id)
    {
        $deliveryOrder = DeliveryOrder::with(['supplier', 'staff', 'items'])->findOrFail($id);

        $pdf = Pdf::loadView('do.pdf', ['deliveryOrder' => $deliveryOrder])->setPaper('a4');
        return $pdf->download("Delivery_Order_{$deliveryOrder->DO_number}.pdf");
    }
    
}
