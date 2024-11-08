<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="issue",type="string")
     * @SWG\Property(property="priority",type="string")
     * @SWG\Property(property="module",type="string")
     * @SWG\Property(property="subject",type="string")
     */
    use Mediable;

    use SoftDeletes;

    protected $fillable = [
        'user_id', 'issue', 'priority', 'module', 'subject',
    ];

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id', 'id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by', 'id');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'issue_id', 'id');
    }

    public function devModule(): BelongsTo
    {
        return $this->belongsTo(DeveloperModule::class, 'module', 'id');
    }
}
