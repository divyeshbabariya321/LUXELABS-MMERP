<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoProcessStatus extends Model
{
    use HasFactory;

    protected $table = 'seo_process_status';

    protected $fillable = [
        'type',
        'label',
    ];
}
