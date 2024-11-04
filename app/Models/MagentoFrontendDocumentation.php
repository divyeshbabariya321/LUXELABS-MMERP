<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\SiteDevelopmentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoFrontendDocumentation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'magento_frontend_docs';

    public function storeWebsiteCategory(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'store_website_category_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
