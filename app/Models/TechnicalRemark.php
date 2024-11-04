<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;

class TechnicalRemark extends Model
{
    use HasFactory;

    public $table = 'technical_debt_remarks';

    protected $fillable = [
        'technical_debt_id',
        'remark',
        'updated_by',
    ];

    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
