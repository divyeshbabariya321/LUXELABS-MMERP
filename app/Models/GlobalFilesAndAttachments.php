<?php

namespace App\Models;
use App\User;
use App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class GlobalFilesAndAttachments extends Model
{
    public $fillable = [
        'id',
        'module_id',
        'module',
        'title',
        'filename',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
