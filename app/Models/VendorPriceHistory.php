<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'vendor_id', 'price', 'currency', 'hisotry'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
