<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualminDomainDnsRecordsHistory extends Model
{
    use HasFactory;

    protected $fillable = ['Virtual_min_domain_id', 'user_id', 'command', 'output', 'status', 'error'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
