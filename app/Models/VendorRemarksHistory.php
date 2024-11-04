<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorRemarksHistory extends Model
{
    use HasFactory;

    protected $fillable = ['vendors_id', 'pre_name',
        'vendor_id',
        'user_id',
        'remarks'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
