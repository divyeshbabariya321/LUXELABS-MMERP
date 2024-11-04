<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\TranslateReplies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepliesTranslatorHistory extends Model
{
    use HasFactory;

    protected $table = 'replies_translator_history';

    protected $fillable = ['translate_replies_id', 'lang', 'translate_text', 'status', 'updated_by_user_id', 'approved_by_user_id'];

    public function translateReply(): BelongsTo
    {
        return $this->belongsTo(TranslateReplies::class, 'translate_replies_id');
    }
}
