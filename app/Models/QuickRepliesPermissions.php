<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuickRepliesPermissions extends Model
{
    use HasFactory;

    protected $table = 'quick_replies_permission';

    protected $fillable = ['user_id', 'lang_id', 'action'];
}
