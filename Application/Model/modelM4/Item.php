<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';
    protected $primaryKey = 'item_no';
    public $timestamps = false;

    protected $fillable = ['item_description', 'quantity', 'DO_id'];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'DO_id', 'DO_id');
    }
}
