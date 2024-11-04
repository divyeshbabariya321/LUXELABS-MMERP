<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class LawyerSpeciality extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="lawyer_specialities",type="string")
     * @SWG\Property(property="title",type="string")
     */

    protected $fillable = ['title'];

    public function lawyers(): HasMany
    {
        return $this->hasMany(Lawyer::class, 'speciality_id');
    }
}
