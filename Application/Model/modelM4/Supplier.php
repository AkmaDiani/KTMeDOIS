<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'Supplier_id';
    public $timestamps = false;
    protected $guarded = [];
}
