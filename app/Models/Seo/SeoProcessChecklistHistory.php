<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoProcessChecklistHistory extends Model
{
    use HasFactory;

    protected $table = 'seo_process_checklist_history';

    protected $fillable = [
        'seo_process_id',
        'field_name',
        'type',
        'is_checked',
        'value',
        'date',
    ];

    /**
     * Modal relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
