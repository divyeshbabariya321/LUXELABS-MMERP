<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreWebsiteCsvFile extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'storewebsite_id', 'status', 'action', 'path', 'message', 'user_id', 'command', 'csv_file_id'];

    public function storewebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'storewebsite_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
