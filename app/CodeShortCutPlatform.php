<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CodeShortCutPlatform extends Model
{
    public $table = 'code_shortcuts_platforms';

    protected $fillable = [
        'name',
    ];

    public function code_shortcuts(): BelongsTo
    {
        return $this->belongsTo(CodeShortcut::class);
    }
}
