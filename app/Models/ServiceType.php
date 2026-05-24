<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'base_charge', 'active'];

    protected $casts = [
        'base_charge' => 'float',
        'active'      => 'boolean',
    ];

    public function complaints()
    {
        return $this->hasMany(FieldComplaint::class);
    }
}
