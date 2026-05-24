<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldPaymentLog extends Model
{
    protected $fillable = ['field_complaint_id', 'amount', 'note', 'paid_at'];

    protected $casts = [
        'amount'  => 'float',
        'paid_at' => 'datetime',
    ];

    public function complaint()
    {
        return $this->belongsTo(FieldComplaint::class, 'field_complaint_id');
    }
}
