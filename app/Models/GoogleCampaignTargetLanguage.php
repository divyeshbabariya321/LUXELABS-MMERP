<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleCampaignTargetLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_language_constant_id',
    ];
}
