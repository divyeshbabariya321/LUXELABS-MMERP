<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevOppsCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function subcategory(): HasMany
    {
        return $this->hasMany(DevOppsSubCategory::class, 'devoops_category_id', 'id', 'status_id');
    }
}
