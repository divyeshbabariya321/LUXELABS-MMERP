<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoMediaSync extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'source_store_website_id', 'dest_store_website_id', 'source_server_ip', 'source_server_dir', 'dest_server_ip', 'dest_server_dir', 'request_data', 'response_data'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->select('name', 'id');
    }

    public function sourcestorewebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'source_store_website_id')->select('title', 'id');
    }

    public function deststorewebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'dest_store_website_id')->select('title', 'id');
    }
}
