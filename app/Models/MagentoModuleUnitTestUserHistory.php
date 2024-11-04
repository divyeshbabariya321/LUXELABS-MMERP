<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleUnitTestUserHistory extends Model
{
    use HasFactory;
    use HasFactory;

    public $table = 'magento_modules_unit_test_user_histories';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function newTestUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_unit_test_user_id');
    }

    public function oldTestUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'old_unit_test_user_id');
    }
}
