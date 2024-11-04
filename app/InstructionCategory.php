<?php

namespace App;
use App\Instruction;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class InstructionCategory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['name'];

    public function instructions(): HasMany
    {
        return $this->hasMany(Instruction::class, 'category_id');
    }
}
