<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = ['job_card_id', 'amount', 'note', 'paid_at'];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }
}
