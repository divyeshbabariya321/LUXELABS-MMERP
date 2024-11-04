<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="TeamUser"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Team;

class TeamUser extends Model
{
    protected $table = 'team_user';

    /**
     * @var string
     *
     * @SWG\Property(property="team_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     */
    protected $fillable = ['id', 'team_id', 'user_id'];

    public function team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }
}
