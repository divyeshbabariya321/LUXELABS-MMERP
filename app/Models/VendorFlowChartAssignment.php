<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorFlowChartAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'master_id',
        'status',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function vendorFlowChartMaster(): BelongsTo
    {
        return $this->belongsTo(VendorFlowChartMaster::class);
    }
}
