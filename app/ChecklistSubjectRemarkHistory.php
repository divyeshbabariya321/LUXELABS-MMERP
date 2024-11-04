<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistSubjectRemarkHistory extends Model
{
    protected $table = 'create_checklist_subject_remark_histories';

    protected $fillable = ['id', 'user_id', 'checklist_id', 'subject_id', 'remark', 'old_remark', 'created_at'];
}
