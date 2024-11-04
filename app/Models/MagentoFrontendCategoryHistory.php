<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\SiteDevelopmentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoFrontendCategoryHistory extends Model
{
    use HasFactory;

    public function newCategory(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'new_category_id');
    }

    public function oldCategory(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'old_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
