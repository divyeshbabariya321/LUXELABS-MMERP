<?php

namespace App;
use App\User;
use App\StoreWebsite;
use App\MagentoCommand;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoMulitipleCommand extends Model
{
    protected $table = 'magento_multiple_commands';

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
