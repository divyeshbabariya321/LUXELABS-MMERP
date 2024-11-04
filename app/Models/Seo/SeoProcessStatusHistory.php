<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoProcessStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'seo_process_status_history';

    protected $fillable = [
        'user_id',
        'seo_process_id',
        'type',
        'seo_process_status_id',
    ];

    /**
     * Modal relationship
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(SeoProcessStatus::class, 'seo_process_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
