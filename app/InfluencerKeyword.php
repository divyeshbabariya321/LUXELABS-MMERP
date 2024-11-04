<?php

namespace App;
use App\Account;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class InfluencerKeyword extends Model
{
    protected $fillable = [
        'name',
        'instagram_account_id',
        'wait_time',
        'no_of_requets',
    ];

    public function next()
    {
        // get next keyword
        return InfluencerKeyword::where('id', '>', $this->id)->orderBy('id')->first();
    }

    public function previous()
    {
        // get previous  keyword
        return InfluencerKeyword::where('id', '<', $this->id)->orderByDesc('id')->first();
    }

    public function instagramAccount(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'instagram_account_id');
    }
}
