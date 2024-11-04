<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\EmailAddress;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoCompanyHistroy extends Model
{
    use HasFactory;

    protected $table = 'seo_company_histories';

    protected $fillable = [
        'seo_company_id',
        'user_id',
        'company_type_id',
        'website_id',
        'da',
        'pa',
        'ss',
        'email_address_id',
        'live_link',
        'status',
    ];

    /**
     * Model relationship
     */
    public function companyType(): BelongsTo
    {
        return $this->belongsTo(SeoCompanyType::class, 'company_type_id');
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'website_id');
    }

    public function emailAddress(): BelongsTo
    {
        return $this->belongsTo(EmailAddress::class, 'email_address_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
