<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldComplaint extends Model
{
    protected $fillable = [
        'complaint_no', 'customer_db_id', 'customer_name', 'phone_no', 'address', 'location_notes',
        'gps_lat', 'gps_lng', 'gps_label',
        'service_type_id', 'service_type_name', 'description', 'priority', 'status',
        'assigned_to', 'assigned_at', 'scheduled_date', 'completed_at', 'completion_notes',
        'photos', 'service_charge', 'discount', 'paid_amount', 'advance_amount',
        'payment_status', 'payment_received', 'invoice_no', 'invoice_date', 'remark', 'created_by',
    ];

    protected $casts = [
        'photos'           => 'array',
        'service_charge'   => 'float',
        'discount'         => 'float',
        'paid_amount'      => 'float',
        'advance_amount'   => 'float',
        'payment_received' => 'boolean',
        'assigned_at'      => 'datetime',
        'completed_at'     => 'datetime',
        'scheduled_date'   => 'date',
        'invoice_date'     => 'date',
    ];

    // ── Accessors ────────────────────────────────────────────────────────────
    public function getGrandTotalAttribute(): float
    {
        $itemsSum = $this->items->sum('total');
        return max(0, (float)$this->service_charge + $itemsSum - (float)$this->discount);
    }

    public function getSubtotalAttribute(): float
    {
        return (float)$this->service_charge + $this->items->sum('total');
    }

    public function getBalanceAttribute(): float
    {
        return max(0, $this->grand_total - (float)$this->paid_amount);
    }

    // ── Auto complaint_no ─────────────────────────────────────────────────────
    public static function nextComplaintNo(): string
    {
        $year  = now()->format('y');
        $month = now()->format('m');
        $prefix = $year . $month;
        $last = static::where('complaint_no', 'like', "FC-{$prefix}%")->max('complaint_no');
        $seq  = $last ? ((int)substr($last, -3) + 1) : 1;
        return "FC-{$prefix}" . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function hasGps(): bool
    {
        return !is_null($this->gps_lat) && !is_null($this->gps_lng);
    }

    public function googleMapsUrl(): ?string
    {
        if (!$this->hasGps()) return null;
        return "https://www.google.com/maps?q={$this->gps_lat},{$this->gps_lng}";
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_db_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function items()
    {
        return $this->hasMany(FieldComplaintItem::class);
    }

    public function paymentLogs()
    {
        return $this->hasMany(FieldPaymentLog::class);
    }
}
