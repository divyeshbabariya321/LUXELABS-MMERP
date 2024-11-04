<?php

namespace App;
use App\MagentoModule;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoModuleM2ErrorAssigneeHistory extends Model
{

    public function magentoModule(): BelongsTo
    {
        return $this->belongsTo(MagentoModule::class);
    }

    public function newAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_assignee_id');
    }

    public function oldAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'old_assignee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
