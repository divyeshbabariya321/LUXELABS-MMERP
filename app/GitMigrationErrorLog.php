<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GitMigrationErrorLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="repository_id",type="string")
     * @SWG\Property(property="branch_name",type="string")
     * @SWG\Property(property="ahead_by",type="string")
     * @SWG\Property(property="behind_by",type="string")
     * @SWG\Property(property="last_commit_author_username",type="string")
     * @SWG\Property(property="last_commit_time",type="string")
     * @SWG\Property(property="error",type="string")
     */
    protected $fillable = ['github_organization_id', 'repository_id', 'branch_name', 'ahead_by', 'behind_by', 'last_commit_author_username', 'last_commit_time', 'error'];
}
