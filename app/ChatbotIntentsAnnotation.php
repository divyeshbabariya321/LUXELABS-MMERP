<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotIntentsAnnotation extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="question_example_id",type="string")
     * @SWG\Property(property="chatbot_keyword_id",type="integer")
     * @SWG\Property(property="start_char_range",type="string")
     * @SWG\Property(property="end_char_range",type="string")
     * @SWG\Property(property="chatbot_value_id",type="integer")
     */
    protected $fillable = [
        'question_example_id', 'chatbot_keyword_id', 'start_char_range', 'end_char_range', 'chatbot_value_id',
    ];

    public function questionExample(): HasOne
    {
        return $this->hasOne(ChatbotQuestionExample::class, 'id', 'question_example_id');
    }

    public function chatbotQuestion(): HasOne
    {
        return $this->hasOne(ChatbotQuestion::class, 'id', 'chatbot_keyword_id');
    }
}
