<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResourceStatusHistory extends Model
{
    use HasFactory;

    public $fillable = ['resource_images_id', 'old_value', 'new_value', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newValue(): BelongsTo
    {
        return $this->belongsTo(ResourceStatus::class, 'new_value');
    }

    public function oldValue(): BelongsTo
    {
        return $this->belongsTo(ResourceStatus::class, 'old_value');
    }
}
