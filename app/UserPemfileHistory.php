<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class UserPemfileHistory extends Model
{
    use SoftDeletes;

    protected $table = 'user_pemfile_history';

    protected $fillable = [
        'user_id',
        'server_id',
        'server_name',
        'server_ip',
        'username',
        'public_key',
        'access_type',
        'user_role',
        'pem_content',
        'action',
        'created_by',
        'extra',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function createby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
