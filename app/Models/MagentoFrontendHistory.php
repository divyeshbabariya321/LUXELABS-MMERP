<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\SiteDevelopmentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoFrontendHistory extends Model
{
    use HasFactory;

    protected $fillable = ['magento_frontend_docs_id', 'store_website_category_id', 'location',  'admin_configuration', 'frontend_configuration', 'updated_by'];

    public function storeWebsiteCategory(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'store_website_category_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
