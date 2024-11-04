<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'id', 'title', 'checklist_id', 'created_at', 'updated_at',
    ];

    public function checklistsubject(): HasMany
    {
        return $this->hasMany(ChecklistSubject::class)->where('user_id', Auth::id());
    }

    public function checklistsubjectRemark(): HasMany
    {
        return $this->hasMany(ChecklistSubjectRemarkHistory::class, 'subject_id', 'id')->orderByDesc('id');
    }
}
