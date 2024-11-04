<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoFrontendParentFolder extends Model
{
    use HasFactory;

    protected $fillable = ['magento_frontend_docs_id', 'user_id',  'parent_folder_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
