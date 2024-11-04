<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;
class RoleUser extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="role_id",type="integer")
     * @SWG\Property(property="role_user",type="string")
     */
    protected $table = 'role_user';

    protected $fillable = ['user_id', 'role_id'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
