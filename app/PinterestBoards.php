<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class PinterestBoards extends Model
{
    protected $table = 'pinterest_business_boards';

    protected $fillable = [
        'pinterest_ads_account_id',
        'board_id',
        'name',
        'description',
        'privacy',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(PinterestAdsAccounts::class, 'id', 'pinterest_ads_account_id');
    }
}
