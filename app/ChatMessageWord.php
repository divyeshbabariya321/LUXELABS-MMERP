<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatMessageWord extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="word",type="string")
     * @SWG\Property(property="total",type="integer")
     */
    protected $fillable = [
        'word', 'total',
    ];

    public function pharases(): HasMany
    {
        return $this->hasMany(ChatMessagePhrase::class, 'word_id', 'id');
    }
}
