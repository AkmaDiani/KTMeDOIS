@extends('layouts.app')

@section('title', $invoice->Invoice_num)

@section('content')
    <div class="page-header">
        <div>
            <span class="eyebrow"><a href="{{ route('invoice.index') }}">← Invoices</a></span>
            <h1 class="code">{{ $invoice->Invoice_num }}</h1>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            @include('partials.status-badge', ['status' => $invoice->Status])
            <a href="{{ route('invoice.export', $invoice->Invoice_id) }}" class="btn btn-ghost">Download PDF</a>
        </div>
    </div>

    <div class="panel">
        @php
            $stages = \App\Models\Invoice::STAGES;
            $currentIndex = array_search($invoice->Status, $stages);
            $rejected = $invoice->isRejected();
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
                <h2>Invoice Details</h2>
                <dl class="kv">
                    <dt>Linked DO</dt>
                    <dd class="code">
                        @if($invoice->deliveryOrder)
                            <a href="{{ route('do.show', $invoice->deliveryOrder->DO_id) }}">{{ $invoice->deliveryOrder->DO_number }}</a>
                        @else — @endif
                    </dd>
                    <dt>Vendor</dt><dd>{{ $invoice->supplier->Supplier_name ?? '—' }}</dd>
                    <dt>Description</dt><dd>{{ $invoice->Description ?? '—' }}</dd>
                    <dt>Issue Date</dt><dd>{{ $invoice->issue_date?->format('d M Y') ?? '—' }}</dd>
                    <dt>Subtotal</dt><dd>RM {{ number_format($invoice->Subtotal, 2) }}</dd>
                    <dt>Tax</dt><dd>RM {{ number_format($invoice->Tax, 2) }}</dd>
                    <dt>Credit Note</dt><dd>RM {{ number_format($invoice->Credit_note, 2) }}</dd>
                    <dt>Total</dt><dd><strong>RM {{ number_format($invoice->Total, 2) }}</strong></dd>
                    <dt>Handled By</dt><dd>{{ $invoice->staff->Username ?? 'Unassigned' }}</dd>
                    @if($invoice->isRejected())
                        <dt>Rejection Reason</dt><dd>{{ $invoice->Reason }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <div>
            @if($invoice->Status === 'Submitted')
                <div class="panel">
                    <h2>Forward to Finance</h2>
                    <form method="POST" action="{{ route('invoice.forward', $invoice->Invoice_id) }}">
                        @csrf
                        <div class="field">
                            <label class="field-label">Finance Officer</label>
                            <select name="staff_id" required>
                                <option value="">Select finance officer…</option>
                                @foreach($financeOfficers as $officer)
                                    <option value="{{ $officer->User_ID }}">{{ $officer->Username }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Forward to Finance</button>
                    </form>
                </div>
            @endif

            @if(in_array($invoice->Status, ['Finance Review', 'Payment Processing']))
                <div class="panel">
                    <h2>Update Stage</h2>
                    <form method="POST" action="{{ route('invoice.updateStatus', $invoice->Invoice_id) }}">
                        @csrf
                        <div class="field">
                            <label class="field-label">Move to</label>
                            <select name="status" required>
                                @if($invoice->Status === 'Finance Review')
                                    <option value="Payment Processing">Payment Processing</option>
                                @elseif($invoice->Status === 'Payment Processing')
                                    <option value="Paid">Paid</option>
                                @endif
                            </select>
                        </div>
                        <button type="submit" class="btn btn-approve" style="width:100%; justify-content:center;">Update Status</button>
                    </form>
                </div>
            @endif

            @if(!in_array($invoice->Status, ['Paid', 'Rejected']))
                <div class="panel">
                    <h2>Reject Invoice</h2>
                    <form method="POST" action="{{ route('invoice.reject', $invoice->Invoice_id) }}">
                        @csrf
                        <div class="field">
                            <label class="field-label">Rejection Reason</label>
                            <textarea name="reason" placeholder="e.g. Missing proof of delivery" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-reject" style="width:100%; justify-content:center;">Reject Invoice</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
