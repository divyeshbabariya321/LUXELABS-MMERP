<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThemeStructureLog extends Model
{

    use HasFactory;

    public $fillable = [
        'theme_id',
        'command',
        'message',
        'status',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(ProjectTheme::class, 'theme_id', 'id');
    }
}
