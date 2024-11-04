<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorFLowChartNotes extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'flow_chart_id', 'notes', 'user_id'];
}
