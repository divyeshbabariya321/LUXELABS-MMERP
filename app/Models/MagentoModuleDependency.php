<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleDependency extends Model
{
    use HasFactory;

    protected $table = 'magento_module_dependancies';

    protected $fillable = ['user_id', 'magento_module_id', 'depency_remark',  'depency_module_issues', 'depency_theme_issues'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
