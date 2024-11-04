<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagnetoReviewStandardHistory extends Model
{
    protected $table = 'magento_review_standard_histories';

    protected $guarded = ['id'];

    protected $fillable = [
        'magento_module_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
