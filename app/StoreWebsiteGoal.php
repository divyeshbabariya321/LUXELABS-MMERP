<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsiteGoalRemark;
class StoreWebsiteGoal extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="goal",type="string")
     * @SWG\Property(property="solution",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = [
        'goal', 'solution', 'store_website_id', 'created_at', 'updated_at',
    ];

    public function remarks(): HasMany
    {
        return $this->hasMany(StoreWebsiteGoalRemark::class, 'store_website_goal_id', 'id');
    }
}
