<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class ScraperKilledHistory extends Model
{
    protected $fillable = [
        'scraper_id',
        'scraper_name',
        'comment',
    ];
}
