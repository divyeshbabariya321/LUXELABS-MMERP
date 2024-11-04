<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;
use App\StoreWebsite;
use App\MagentoCommand;

class MagentoMultipleCron extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'command_id',
        'user_id',
        'website_ids',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'website_ids');
    }

    public function command(): BelongsTo
    {
        return $this->belongsTo(MagentoCommand::class, 'command_id');
    }
}
