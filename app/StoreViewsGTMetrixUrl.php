<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class StoreViewsGTMetrixUrl extends Model
{
    use Mediable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'store_views_gt_metrix_url';

    protected $fillable = [
        'account_id',
        'store_view_id',
        'store_name',
        'website_url',
        'process',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'resources' => 'array',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(StoreGTMetrixAccount::class, 'account_id');
    }
}
