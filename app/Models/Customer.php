<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'phone',
        'email',
        'nic',
        'address',
        'gps_lat',
        'gps_lng',
        'gps_label',
        'gps_raw_link',
    ];

    public function fieldComplaints()
    {
        return $this->hasMany(FieldComplaint::class, 'customer_db_id');
    }

    public function jobCards()
    {
        return $this->hasMany(JobCard::class, 'phone_no', 'phone');
    }

    public static function nextCustomerId(): string
    {
        $last = static::orderByDesc('id')->value('customer_id');
        if (!$last) return 'CUS-001';
        preg_match('/(\d+)$/', $last, $m);
        $next = isset($m[1]) ? (int)$m[1] + 1 : 1;
        return 'CUS-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Parse lat/lng from common Google Maps / WhatsApp location share links.
     * Returns ['lat' => float, 'lng' => float] or null.
     */
    public static function parseGpsLink(string $input): ?array
    {
        $input = trim($input);

        // Plain coords: "6.9271, 79.8612" or "6.9271 79.8612"
        if (preg_match('/^(-?\d{1,3}\.\d+)[,\s]+(-?\d{1,3}\.\d+)$/', $input, $m)) {
            return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
        }

        // Google Maps short/full: ?q=lat,lng  or  @lat,lng  or  ll=lat,lng
        if (preg_match('/[?&@]q=(-?\d+\.\d+),(-?\d+\.\d+)/i', $input, $m) ||
            preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/i', $input, $m) ||
            preg_match('/[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)/i', $input, $m) ||
            preg_match('/maps\/place\/[^\/]+\/@(-?\d+\.\d+),(-?\d+\.\d+)/i', $input, $m)) {
            return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
        }

        // WhatsApp: geo:lat,lng
        if (preg_match('/geo:(-?\d+\.\d+),(-?\d+\.\d+)/i', $input, $m)) {
            return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
        }

        return null;
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
}
