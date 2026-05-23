<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['job_card_id', 'description', 'unit_price', 'qty', 'total'];

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }
}
