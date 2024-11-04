<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class ScraperScreenshotHistory extends Model
{
    use Mediable;

    protected $fillable = [
        'scraper_id',
        'scraper_name',
    ];
}
