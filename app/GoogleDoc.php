<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\GoogleDocsCategory;
use Illuminate\Database\Eloquent\Model;

class GoogleDoc extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(GoogleDocsCategory::class, 'id', 'category');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
