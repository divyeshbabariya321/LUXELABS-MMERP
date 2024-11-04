<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\PostmanStatus;

class PostmanRequestCreate extends Model
{

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'body_json' => 'array',
        ];
    }

    public function latestRes(): HasOne
    {
        return $this->hasOne(PostmanResponse::class, 'request_id', 'id');
    }

    public function postmanStatus(): BelongsTo
    {
        return $this->belongsTo(PostmanStatus::class, 'status_id', 'id');
    }

    public static function dropdownRequestNames()
    {
        return self::orderBy('request_name')->pluck('request_name', 'request_name')->toArray();
    }
}
