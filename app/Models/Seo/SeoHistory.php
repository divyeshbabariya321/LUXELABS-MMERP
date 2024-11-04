<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'seo_process_id',
        'message',
    ];

    /**
     * Model relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function msgUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'message');
    }
}
