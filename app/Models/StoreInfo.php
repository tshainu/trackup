<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreInfo extends Model
{
    protected $table = 'store_info';
    protected $fillable = ['store_name','registration_no','store_address','phone_no1','phone_no2','owner_name','owner_phoneno','owner_address','logo'];
}
