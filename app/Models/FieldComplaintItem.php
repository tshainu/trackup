<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldComplaintItem extends Model
{
    protected $fillable = ['field_complaint_id', 'description', 'qty', 'unit_price', 'total'];

    protected $casts = [
        'qty'        => 'integer',
        'unit_price' => 'float',
        'total'      => 'float',
    ];

    public function complaint()
    {
        return $this->belongsTo(FieldComplaint::class, 'field_complaint_id');
    }
}
