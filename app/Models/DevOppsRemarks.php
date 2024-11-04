<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevOppsRemarks extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_category_id', 'sub_category_id', 'remarks', 'added_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
