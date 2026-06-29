@extends('layouts.app')

@section('title', $deliveryOrder->DO_number)

@section('content')
    <div class="page-header">
        <div>
            <span class="eyebrow"><a href="{{ route('do.index') }}">← Delivery Orders</a></span>
            <h1 class="code">{{ $deliveryOrder->DO_number }}</h1>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            @include('partials.status-badge', ['status' => $deliveryOrder->Status])
            <a href="{{ route('do.export', $deliveryOrder->DO_id) }}" class="btn btn-ghost">Download PDF</a>
        </div>
    </div>

    {{-- Rail tracker: shows real workflow sequence (Submitted -> Under Review -> Approved),
         or a single rejected stop if the DO was rejected. --}}
    <div class="panel">
        @php
            $stages = \App\Models\DeliveryOrder::STAGES;
            $currentIndex = array_search($deliveryOrder->Status, $stages);
            $rejected = $deliveryOrder->isRejected();
        @endphp
        <div class="tracker">
            <div class="tracker-line">
                @if(!$rejected)
                    <div class="fill" style="width: {{ $currentIndex > 0 ? ($currentIndex / (count($stages)-1)) * 100 : 0 }}%"></div>
                @endif
                @foreach($stages as $i => $stage)
                    <div class="station {{ $rejected ? '' : ($i < $currentIndex ? 'done' : ($i === $currentIndex ? 'current' : '')) }}">
                        <div class="dot"></div>
                        <label>{{ $stage }}</label>
                    </div>
                @endforeach
                @if($rejected)
                    <div class="station rejected">
                        <div class="dot"></div>
                        <label>Rejected</label>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="detail-grid">
        <div>
            <div class="panel">
                <h2>Delivery Order Details</h2>
                <dl class="kv">
                    <dt>PO Number</dt><dd class="code">{{ $deliveryOrder->PO_number }}</dd>
                    <dt>Vendor</dt><dd>{{ $deliveryOrder->supplier->Supplier_name ?? '—' }}</dd>
                    <dt>Contact Person</dt><dd>{{ $deliveryOrder->supplier->Contact_person ?? '—' }}</dd>
                    <dt>Submitted On</dt><dd>{{ $deliveryOrder->created_date?->format('d M Y, h:i A') ?? '—' }}</dd>
                    <dt>Reviewer</dt><dd>{{ $deliveryOrder->staff->Username ?? 'Unassigned' }}</dd>
                    @if($deliveryOrder->isRejected())
                        <dt>Rejection Reason</dt><dd>{{ $deliveryOrder->Reason }}</dd>
                    @endif
                </dl>
            </div>

            <div class="panel">
                <h2>Items</h2>
                @if($deliveryOrder->items->isEmpty())
                    <p class="hint">No line items recorded for this delivery order.</p>
                @else
                    <table>
                        <thead><tr><th>Description</th><th>Quantity</th></tr></thead>
                        <tbody>
                            @foreach($deliveryOrder->items as $item)
                                <tr><td>{{ $item->item_description }}</td><td>{{ $item->quantity }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($deliveryOrder->invoices->isNotEmpty())
                <div class="panel">
                    <h2>Linked Invoices</h2>
                    <table>
                        <thead><tr><th>Invoice No.</th><th>Total</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @foreach($deliveryOrder->invoices as $invoice)
                                <tr>
                                    <td class="code">{{ $invoice->Invoice_num }}</td>
                                    <td>RM {{ number_format($invoice->Total, 2) }}</td>
                                    <td>@include('partials.status-badge', ['status' => $invoice->Status])</td>
                                    <td><a href="{{ route('invoice.show', $invoice->Invoice_id) }}">View →</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div>
            <div class="panel">
                <h2>Assign Reviewer</h2>
                <form method="POST" action="{{ route('do.assign', $deliveryOrder->DO_id) }}">
                    @csrf
                    <div class="field">
                        <label class="field-label">KTM Officer</label>
                        <select name="staff_id" required>
                            <option value="">Select officer…</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->User_ID }}" @selected($deliveryOrder->Staff_id == $officer->User_ID)>
                                    {{ $officer->Username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Assign</button>
                </form>
            </div>

            @if(!in_array($deliveryOrder->Status, ['Approved', 'Rejected']))
                <div class="panel">
                    <h2>Decision</h2>

                    <form method="POST" action="{{ route('do.approve', $deliveryOrder->DO_id) }}">
                        @csrf
                        <button type="submit" class="btn btn-approve" style="width:100%; justify-content:center;">Approve Delivery Order</button>
                    </form>

                    <form method="POST" action="{{ route('do.reject', $deliveryOrder->DO_id) }}" style="margin-top:12px;">
                        @csrf
                        <div class="field">
                            <label class="field-label">Rejection Reason</label>
                            <textarea name="reason" placeholder="e.g. Invalid supporting document" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-reject" style="width:100%; justify-content:center;">Reject Delivery Order</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
