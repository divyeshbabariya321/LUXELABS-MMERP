<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleUnitTestStatusHistory extends Model
{
    use HasFactory;

    public $table = 'magento_modules_unit_test_statuses_histories';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function newTestStatus(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleUnitTestStatus::class, 'new_unit_test_status_id');
    }

    public function oldTestStatus(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleUnitTestStatus::class, 'old_unit_test_status_id');
    }
}
