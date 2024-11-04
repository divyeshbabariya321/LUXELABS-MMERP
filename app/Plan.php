<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PlanAction;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{

    public function subList($id)
    {
        return $this->where('parent_id', $id)->get();
    }

    public function getPlanActionStrength(): HasMany
    {
        return $this->hasMany(PlanAction::class, 'plan_id', 'id')
            ->where('plan_action_type', 1);
    }

    public function getPlanActionWeakness(): HasMany
    {
        return $this->hasMany(PlanAction::class, 'plan_id', 'id')
            ->where('plan_action_type', 2);
    }

    public function getPlanActionOpportunity(): HasMany
    {
        return $this->hasMany(PlanAction::class, 'plan_id', 'id')
            ->where('plan_action_type', 3);
    }

    public function getPlanActionThreat(): HasMany
    {
        return $this->hasMany(PlanAction::class, 'plan_id', 'id')
            ->where('plan_action_type', 4);
    }
}
