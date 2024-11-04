<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoFrontendRemark extends Model
{
    use HasFactory;

    protected $fillable = ['magento_frontend_docs_id', 'user_id',  'remark'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
