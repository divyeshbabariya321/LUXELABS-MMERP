<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class StoreGTMetrixAccount extends Model
{
    use Mediable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'store_gt_metrix_account';

    protected $fillable = [
        'email',
        'password',
        'account_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'resources' => 'array',
        ];
    }
}
