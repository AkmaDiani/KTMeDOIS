<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';
    protected $primaryKey = 'Invoice_id';
    public $timestamps = false;

    protected $fillable = [
        'Invoice_num', 'DO_id', 'supplier_ID', 'Staff_id', 'issue_date',
        'Subtotal', 'Tax', 'Total', 'Status', 'Reason', 'Created_At',
        'Description', 'Credit_note',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'Created_At' => 'datetime',
        'Subtotal' => 'decimal:2',
        'Tax' => 'decimal:2',
        'Total' => 'decimal:2',
        'Credit_note' => 'decimal:2',
    ];

    public const STAGES = ['Submitted', 'Finance Review', 'Payment Processing', 'Paid'];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'DO_id', 'DO_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_ID', 'Supplier_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'Staff_id', 'User_ID');
    }

    public function isRejected(): bool
    {
        return $this->Status === 'Rejected';
    }
}
