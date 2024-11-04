<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklist';

    protected $fillable = [
        'category_name',
        'sub_category_name',
    ];

    protected $primaryKey = 'id';

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
