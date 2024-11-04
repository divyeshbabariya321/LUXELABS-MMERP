<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use App\TemplateModification;

class Template extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")

     * @SWG\Property(property="no_of_images",type="string")
     * @SWG\Property(property="auto_generate_product",type="string")
     */
    use Mediable;

    protected $fillable = [
        'name',
        'no_of_images',
        'auto_generate_product',
        'uid',
        'available_modifications',

    ];

    public function modifications(): HasMany
    {
        return $this->hasMany(TemplateModification::class, 'template_id', 'id');
    }
}
