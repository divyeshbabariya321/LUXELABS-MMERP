<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteIssue extends Model
{

    protected $fillable = ['store_website_id', 'project_id', 'issue_id', 'title', 'desc', 'title_page'];
}
