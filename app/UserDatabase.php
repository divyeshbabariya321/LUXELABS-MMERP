<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="UserDatabase"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\UserDatabaseTable;

class UserDatabase extends Model
{
    protected $fillable = [
        'database',
        'username',
        'password',
        'user_id',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userDatabaseTables(): HasMany
    {
        return $this->hasMany(UserDatabaseTable::class, 'user_database_id', 'id');
    }
}
