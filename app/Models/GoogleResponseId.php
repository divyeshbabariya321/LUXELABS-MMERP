<?php

namespace App\Models;
use App\Models\GoogleDialogAccount;
use App\Models;
use App\ChatbotQuestion;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleResponseId extends Model
{
    use HasFactory;

    protected $fillable = ['chatbot_question_id', 'google_response_id', 'google_dialog_account_id'];

    public function questionModal(): HasOne
    {
        return $this->hasOne(ChatbotQuestion::class, 'id', 'chatbot_question_id');
    }

    public function googleAccountModal(): HasOne
    {
        return $this->hasOne(GoogleDialogAccount::class, 'id', 'google_dialog_account_id');
    }
}
