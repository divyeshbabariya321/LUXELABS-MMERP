<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UicheckAttachement extends Model
{

    protected $fillable = ['user_id', 'uicheck_id', 'subject', 'description', 'filename'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uicheck(): BelongsTo
    {
        return $this->belongsTo(Uicheck::class);
    }
}
