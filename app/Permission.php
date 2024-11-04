<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function getRoleIds()
    {
        return $this->roles()->allRelatedIds();
    }

    public function getRoleIdsInArray()
    {
        return $this->roles()->allRelatedIds()->toArray();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
