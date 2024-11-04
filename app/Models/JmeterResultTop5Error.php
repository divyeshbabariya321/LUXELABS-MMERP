<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\loadTesting;

class JmeterResultTop5Error extends Model
{
    use HasFactory;
    
    public $guarded = [];           

    public function loadTesting(): BelongsTo
    {
        return $this->belongsTo(loadTesting::class);
    }
}
