<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoCompanyType extends Model
{
    use HasFactory;

    protected $table = 'seo_company_type';

    protected $fillable = [
        'name',
    ];
}
