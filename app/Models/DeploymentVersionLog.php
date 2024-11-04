<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeploymentVersionLog extends Model
{
    use HasFactory;

    protected $fillable = ['deployement_version_id', 'user_id', 'build_number', 'error_message', 'error_code', 'created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deployversion(): BelongsTo
    {
        return $this->belongsTo(DeploymentVersion::class, 'deployement_version_id');
    }
}
