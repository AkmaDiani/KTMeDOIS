<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $table = 'do';
    protected $primaryKey = 'DO_id';
    public $timestamps = false;

    protected $fillable = [
        'DO_number', 'PO_number', 'supplier_ID', 'Staff_id',
        'DO_link', 'Proof_link', 'Status', 'Reason', 'created_by', 'created_date',
    ];

    protected $casts = [
        'created_date' => 'datetime',
    ];

    // Workflow stages, in order. Used to draw the status tracker in the views.
    public const STAGES = ['Submitted', 'Under Review', 'Approved'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_ID', 'Supplier_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'Staff_id', 'User_ID');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'DO_id', 'DO_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'DO_id', 'DO_id');
    }

    public function isRejected(): bool
    {
        return $this->Status === 'Rejected';
    }
}
