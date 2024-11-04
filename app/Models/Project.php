<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'serverenv',
        'job_name',
        'project_type',
    ];

    public function getStoreWebsiteNamesAttribute()
    {
        return $this->storeWebsites->pluck('title')->implode(', ');
    }

    public function storeWebsites(): BelongsToMany
    {
        return $this->belongsToMany(StoreWebsite::class, 'project_store_website');
    }
}
