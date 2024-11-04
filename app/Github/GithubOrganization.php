<?php

namespace App\Github;
use App\Github\GithubRepository;

use App\User;
use Illuminate\Database\Eloquent\Factories\BelongsToManyRelationship;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GithubOrganization extends Model
{
    use HasFactory;

    // RELATIONS
    public function repos(): HasMany
    {
        return $this->hasMany(GithubRepository::class, 'github_organization_id', 'id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
