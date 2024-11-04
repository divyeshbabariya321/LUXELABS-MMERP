<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class ConfigRefactorSection extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['name', 'type'];

    public function configRefactor(): HasOne
    {
        return $this->hasOne(ConfigRefactor::class);
    }

    const NONDEFAULT = 'ND';

    const DEFAULT = 'DE';

    public static $types = [
        self::NONDEFAULT => 'Non Default',
        self::DEFAULT    => 'Default',
    ];
}
