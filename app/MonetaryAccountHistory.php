<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class MonetaryAccountHistory extends Model
{
    protected $fillable = ['model_id', 'model_type', 'amount', 'note', 'monetary_account_id', 'user_id', 'created_at', 'updated_at'];

    public function model(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }
}
