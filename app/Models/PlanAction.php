<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;

class PlanAction extends Model
{

    public $fillable = [
        'plan_id',
        'plan_action',
        'plan_action_type',
        'created_by',
    ];

    public function getAdminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
