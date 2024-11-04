<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BugTracker extends Model
{
    protected $guarded = ['id'];

    public function type()
    {
        $this->belongsTo(BugType::class, 'bug_type_id', 'id');
    }

    public function environment()
    {
        $this->belongsTo(BugEnvironment::class, 'bug_environment_id', 'id');
    }

    public function severity()
    {
        $this->belongsTo(BugSeverity::class, 'bug_severity_id', 'id');
    }

    public function module()
    {
        $this->belongsTo(SiteDevelopmentCategory::class, 'module_id', 'id');
    }

    public function userassign(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_to', 'id');
    }

    public function whatsappAll($needBroadcast = false): HasMany
    {
        if ($needBroadcast) {
            return $this->hasMany(ChatMessage::class, 'bug_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(ChatMessage::class, 'bug_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    public function chatlatest(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'bug_id');
    }
}
