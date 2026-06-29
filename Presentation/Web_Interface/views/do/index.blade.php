@extends('layouts.app')

@section('title', 'Delivery Orders')

@section('content')
    <div class="page-header">
        <div>
            <span class="eyebrow">Module 4 — Internal Review</span>
            <h1>Delivery Orders</h1>
        </div>
    </div>

    <div class="pillbar">
        <a href="{{ route('do.index') }}" class="{{ !$activeStatus ? 'active' : '' }}">All</a>
        @foreach($statuses as $s)
            <a href="{{ route('do.index', ['status' => $s]) }}" class="{{ $activeStatus === $s ? 'active' : '' }}">{{ $s }}</a>
        @endforeach
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>DO Number</th>
                    <th>PO Number</th>
                    <th>Vendor</th>
                    <th>Reviewer</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveryOrders as $do)
                    <tr>
                        <td class="code">{{ $do->DO_number }}</td>
                        <td class="code">{{ $do->PO_number }}</td>
                        <td>{{ $do->supplier->Supplier_name ?? '—' }}</td>
                        <td>{{ $do->staff->Username ?? 'Unassigned' }}</td>
                        <td>@include('partials.status-badge', ['status' => $do->Status])</td>
                        <td>{{ $do->created_date?->format('d M Y') ?? '—' }}</td>
                        <td><a href="{{ route('do.show', $do->DO_id) }}">Review →</a></td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">No delivery orders found for this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
