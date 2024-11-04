<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevOppsSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'devoops_category_id', 'name',
    ];

    public function devoops_category(): BelongsTo
    {
        return $this->belongsTo(DevOppsCategories::class, 'devoops_category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(DevOopsStatus::class, 'status_id');
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(DevOppsRemarks::class, 'sub_category_id', 'id');
    }
}
