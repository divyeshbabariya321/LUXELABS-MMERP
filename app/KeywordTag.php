<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeywordTag extends Model
{
    protected $fillable = ['keyword_id', 'tag_id'];
}
