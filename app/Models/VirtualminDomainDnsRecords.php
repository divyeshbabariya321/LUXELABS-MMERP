<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\VirtualminDomain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualminDomainDnsRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'Virtual_min_domain_id',
        'identifier_id',
        'dns_type',
        'type',
        'priority',
        'content',
        'name',
        'domain_with_dns_name',
        'proxied',
    ];

    public function VirtualminDomain(): BelongsTo
    {
        return $this->belongsTo(VirtualminDomain::class, 'Virtual_min_domain_id');
    }
}
