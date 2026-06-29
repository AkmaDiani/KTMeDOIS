<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    // Named "NotificationLog" rather than "Notification" so it doesn't
    // collide with Laravel's built-in Illuminate\Notifications\Notification.
    protected $table = 'notification';
    protected $primaryKey = 'Notification_ID';
    public $timestamps = false;

    protected $fillable = [
        'User_ID', 'Supplier_id', 'Type', 'Content', 'Status', 'Creates_At',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'User_ID', 'User_ID');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'Supplier_id', 'Supplier_id');
    }
}
