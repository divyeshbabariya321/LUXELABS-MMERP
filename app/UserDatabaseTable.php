<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="UserDatabase"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\UserDatabase;

class UserDatabaseTable extends Model
{
    protected $fillable = [
        'name',
        'user_database_id',
    ];

    public function userDatabase(): HasOne
    {
        return $this->hasOne(UserDatabase::class, 'id', 'user_database_id');
    }
}
