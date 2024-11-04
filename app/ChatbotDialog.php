<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotDialog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="title",type="string")
     * @SWG\Property(property="parent_id",type="integer")
     * @SWG\Property(property="match_condition",type="string")
     * @SWG\Property(property="workspace_id",type="integer")
     * @SWG\Property(property="previous_sibling",type="string")
     * @SWG\Property(property="metadata",type="string")
     */
    protected $fillable = [
        'name', 'title', 'parent_id', 'match_condition', 'workspace_id', 'previous_sibling', 'metadata', 'response_type', 'dialog_type',
    ];

    public function response(): HasMany
    {
        return $this->hasMany(ChatbotDialogResponse::class, 'chatbot_dialog_id', 'id');
    }

    public function parentResponse(): HasMany
    {
        return $this->hasMany(ChatbotDialog::class, 'parent_id', 'id');
    }

    public function previous(): HasOne
    {
        return $this->hasOne(ChatbotDialog::class, 'id', 'previous_sibling');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(ChatbotDialog::class, 'id', 'parent_id');
    }

    public function singleResponse(): HasOne
    {
        return $this->hasOne(ChatbotDialogResponse::class, 'chatbot_dialog_id', 'id');
    }

    public function getPreviousSiblingName()
    {
        return ($this->previous) ? $this->previous->name : null;
    }

    public function getParentName()
    {
        return ($this->parent) ? $this->parent->name : null;
    }

    public function multipleCondition(): HasMany
    {
        return $this->hasMany(ChatbotDialog::class, 'parent_id', 'id');
    }

    public static function allSuggestedOptions()
    {

        // $question = ChatbotQuestion::where('keyword_or_question', 'intent')
        //         ->select('value')
        //         ->get()
        //         ->mapWithKeys(function ($item) {
        //             return ['#'.$item->value => '#'.$item->value];
        //         })
        //         ->toArray();

        //     $keywords = ChatbotQuestion::where('keyword_or_question', 'entity')
        //         ->selectRaw("CONCAT('@', value) as value")
        //         ->pluck('value')
        //         ->toArray();
        $suggestedOptions = DB::table(DB::raw("(SELECT concat('#', value) as value FROM chatbot_questions WHERE keyword_or_question = 'intent' 
            UNION 
            SELECT concat('@', value) as value FROM chatbot_questions WHERE keyword_or_question = 'entity') as combined"))
            ->select('value')
            ->get()
            ->pluck('value', 'value')
            ->toArray();

        return $suggestedOptions;
    }
}
