<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMilestone extends Model
{
    protected $fillable = [
        'shop_id', 'field_complaint_id', 'title', 'order', 'status',
        'notes', 'staff_id', 'completed_at', 'transferred_to',
        'transfer_reason', 'help_requested', 'help_notes',
    ];

    protected $casts = [
        'completed_at'   => 'datetime',
        'help_requested' => 'boolean',
    ];

    public function complaint()
    {
        return $this->belongsTo(FieldComplaint::class, 'field_complaint_id');
    }

    public function staff()
    {
        return $this->belongsTo(Employee::class, 'staff_id');
    }

    public function transferredEmployee()
    {
        return $this->belongsTo(Employee::class, 'transferred_to');
    }
}
