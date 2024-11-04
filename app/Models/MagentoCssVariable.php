<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\User;
use App\MagentoCssVariableJobLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoCssVariable extends Model
{
    use HasFactory;

    public $fillable = [
        'project_id',
        'filename',
        'file_path',
        'variable',
        'value',
        'create_by',
        'is_verified',
    ];

    const VERIFIED = 1;

    const NOTVERIFIED = 0;

    public static $verifiedOptions = [
        self::VERIFIED    => 'Yes',
        self::NOTVERIFIED => 'No',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    public function lastLog(): HasOne
    {
        return $this->hasOne(MagentoCssVariableJobLog::class)
            ->orderByDesc('created_at')
            ->latest();
    }
}
