<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Carbon\Carbon;
use App\Models\JmeterResultApdex;
use App\Models\JmeterResultError;
use App\Models\JmeterResultStatistic;
use App\Models\JmeterResultTop5Error;

class loadTesting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function getCreatedAtAttribute($v)
    {
        $createdAt = Carbon::parse($v);
        return $this->attributes['created_at'] = $createdAt->format('d-m-Y h:m');
    }

    public function jmeterResultApdex(): HasMany
    {
        return $this->hasMany(JmeterResultApdex::class);
    }

    public function jmeterResultError(): HasMany
    {
        return $this->hasMany(JmeterResultError::class);
    }

    public function jmeterResultStatistic(): HasMany
    {
        return $this->hasMany(JmeterResultStatistic::class);
    }

    public function jmeterResultTop5Error(): HasMany
    {
        return $this->hasMany(JmeterResultTop5Error::class);
    }

}
