<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\EmailCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailCategoryHistory extends Model
{
    use HasFactory;

    protected $table = 'email_category_change_history';

    protected $fillable = [
        'user_id',
        'category_id',
        'old_user_id',
        'old_category_id',
        'email_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EmailCategory::class, 'category_id', 'id');
    }

    public function oldCategory(): BelongsTo
    {
        return $this->belongsTo(EmailCategory::class, 'old_category_id', 'id');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'old_user_id', 'id');
    }
}
