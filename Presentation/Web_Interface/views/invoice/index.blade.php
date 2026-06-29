@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
    <div class="page-header">
        <div>
            <span class="eyebrow">Module 4 — Internal Review</span>
            <h1>Invoices</h1>
        </div>
    </div>

    <div class="pillbar">
        <a href="{{ route('invoice.index') }}" class="{{ !$activeStatus ? 'active' : '' }}">All</a>
        @foreach($statuses as $s)
            <a href="{{ route('invoice.index', ['status' => $s]) }}" class="{{ $activeStatus === $s ? 'active' : '' }}">{{ $s }}</a>
        @endforeach
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Invoice No.</th>
                    <th>Linked DO</th>
                    <th>Vendor</th>
                    <th>Total</th>
                    <th>Handler</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="code">{{ $invoice->Invoice_num }}</td>
                        <td class="code">{{ $invoice->deliveryOrder->DO_number ?? '—' }}</td>
                        <td>{{ $invoice->supplier->Supplier_name ?? '—' }}</td>
                        <td>RM {{ number_format($invoice->Total, 2) }}</td>
                        <td>{{ $invoice->staff->Username ?? 'Unassigned' }}</td>
                        <td>@include('partials.status-badge', ['status' => $invoice->Status])</td>
                        <td><a href="{{ route('invoice.show', $invoice->Invoice_id) }}">Review →</a></td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">No invoices found for this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
