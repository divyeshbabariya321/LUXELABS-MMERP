<?php

namespace App\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaveZoomMeetingRecordings extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['file_name', 'description'];
}
