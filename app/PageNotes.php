<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\PageNotesCategories;

class PageNotes extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="url",type="strng")
     * @SWG\Property(property="note",type="string")
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     */
    protected $fillable = [
        'url', 'category_id', 'note', 'user_id', 'title',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function pageNotesCategories(): HasOne
    {
        return $this->hasOne(PageNotesCategories::class, 'id', 'category_id');
    }
}
