<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    // NOTE: the table name in the DB literally has a space: `ktm staff`.
    // Laravel/MySQL handle this fine because table names get wrapped in
    // backticks automatically, but never rename this without also
    // renaming the table (and checking with your teammates first, since
    // other modules read from it too).
    protected $table = 'ktm staff';
    protected $primaryKey = 'User_ID';
    public $timestamps = false;

    protected $fillable = [
        'Username', 'Password_Hash', 'Role', 'Last_Login', 'Email', 'Status',
    ];

    protected $hidden = ['Password_Hash'];

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'Staff_id', 'User_ID');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'Staff_id', 'User_ID');
    }
}
