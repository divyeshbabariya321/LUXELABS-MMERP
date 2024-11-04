<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfigRefactorUserHistory extends Model
{
    use HasFactory;

    protected $appends = [
        'old_user_name',
        'new_user_name',
    ];

    public $fillable = [
        'config_refactor_id',
        'old_user',
        'new_user',
        'user_id',
    ];

    public function configRefactor(): BelongsTo
    {
        return $this->belongsTo(ConfigRefactor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_user');
    }

    public function oldUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'old_user');
    }

    public function getOldUserNameAttribute()
    {
        return $this->oldUser?->name;
    }

    public function getNewUserNameAttribute()
    {
        return $this->newUser?->name;
    }
}
