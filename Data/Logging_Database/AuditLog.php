<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'auditlog';
    protected $primaryKey = 'Log_ID';
    public $timestamps = false;

    protected $fillable = ['User_ID', 'Action', 'Affected_Record', 'Timestamp'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'User_ID', 'User_ID');
    }

    /**
     * Convenience helper so controllers can write one line per action:
     * AuditLog::record('Approved Delivery Order DO-2026-0001', 'DO-2026-0001');
     */
    public static function record(string $action, string $affectedRecord): self
    {
        return self::create([
            'User_ID' => session('staff_id'),
            'Action' => $action,
            'Affected_Record' => $affectedRecord,
            'Timestamp' => now(),
        ]);
    }
}
