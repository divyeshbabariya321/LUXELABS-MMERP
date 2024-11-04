<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostmanApiIssueFixDoneHistory extends Model
{
    use HasFactory;

    protected $fillable = ['postman_create_id', 'old_value', 'new_value', 'user_id'];

    protected $appends = ['new_value_text', 'old_value_text'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for the new 'old_value_text' attribute
    public function getOldValueTextAttribute()
    {
        if ($this->old_value === 0) {
            return 'No';
        }

        if ($this->old_value === 1) {
            return 'Yes';
        }

        if ($this->old_value === 2) {
            return 'Lead Verified';
        }

        return '-';
    }

    // Accessor for the new 'new_value_text' attribute
    public function getNewValueTextAttribute()
    {
        if ($this->new_value === 0) {
            return 'No';
        }

        if ($this->new_value === 1) {
            return 'Yes';
        }

        if ($this->new_value === 2) {
            return 'Lead Verified';
        }

        return '-';
    }
}
