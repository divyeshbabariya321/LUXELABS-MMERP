<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorFlowChart extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_by', 'sorting', 'master_id'];

    public function master(): BelongsTo
    {
        return $this->belongsTo(VendorFlowChartMaster::class);
    }
}
