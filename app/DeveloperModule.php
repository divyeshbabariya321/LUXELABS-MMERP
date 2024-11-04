<?php

namespace App;
use App\DeveloperTask;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeveloperModule extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(DeveloperTask::class, 'module_id');
    }
}
