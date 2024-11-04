<?php

namespace App\Models;
use App\User;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevOppsSubCategoryDocument extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'description', 'created_by', 'created_at', 'devoops_task_id'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
