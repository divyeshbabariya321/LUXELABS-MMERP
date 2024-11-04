<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonitUnitCommandRunLogs extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'xmlid', 'request_data', 'response_data'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->select('name', 'id');
    }
}
