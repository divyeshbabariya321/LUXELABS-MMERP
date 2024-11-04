<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoProcessChecklist extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'seo_process_checklist';

    protected $fillable = [
        'seo_process_id',
        'field_name',
        'type',
        'is_checked',
        'value',
        'date',
    ];
}
