<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class GmailData extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="gmail_data",type="string")
     * @SWG\Property(property="images",type="string")
     * @SWG\Property(property="tags",type="string")
     */
    protected $table = 'gmail_data';

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'tags'   => 'array',
        ];
    }

    public function gmailDataMedia(): HasMany
    {
        return $this->hasMany(GmailDataMedia::class);
    }
}
