@php
    $slug = match($status) {
        'Submitted' => 'submitted',
        'Under Review' => 'review',
        'Approved' => 'approved',
        'Rejected' => 'rejected',
        'Finance Review' => 'finance',
        'Payment Processing' => 'processing',
        'Paid' => 'paid',
        default => 'submitted',
    };
@endphp
<span class="badge badge-{{ $slug }}">{{ $status }}</span>
