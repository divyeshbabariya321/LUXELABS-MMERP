<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\SiteDevelopment;
use App\SiteDevelopmentStatus;
class SiteDevelopmentStatusHistory extends Model
{
    protected $fillable = [
        'site_development_id',
        'status_id',
        'user_id',
    ];

    public function siteDevelopment(): HasOne
    {
        return $this->hasOne(SiteDevelopment::class, 'id', 'site_development_id');
    }

    public function status(): HasOne
    {
        return $this->hasOne(SiteDevelopmentStatus::class, 'id', 'status_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
