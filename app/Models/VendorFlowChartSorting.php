<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorFlowChartSorting extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'flow_chart_id', 'sorting_f'];

    public function flowchart(): BelongsTo
    {
        return $this->belongsTo(VendorFlowChart::class, 'flow_chart_id');
    }
}
