<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechnicalFrameWork extends Model
{
    use HasFactory;

    public $table = 'technical_frameworks';

    protected $fillable = [
        'name',
    ];

    public function technical_depts(): BelongsTo
    {
        return $this->belongsTo(TechnicalDebt::class);
    }
}
