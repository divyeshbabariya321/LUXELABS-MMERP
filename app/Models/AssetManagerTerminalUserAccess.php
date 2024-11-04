<?php

namespace App\Models;
use App\User;
use App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetManagerTerminalUserAccess extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['assets_management_id', 'created_by', 'username', 'password'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by')->select('name', 'id');
    }
}
