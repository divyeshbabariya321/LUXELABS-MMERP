<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="taskid",type="integer")
     * @SWG\Property(property="remark",type="string")
     * @SWG\Property(property="module_type",type="string")
     * @SWG\Property(property="user_name",type="string")
     */
    protected $fillable = [
        'remark',
        'taskid',
        'module_type',
        'user_name',
    ];

    public function subnotes(): HasMany
    {
        return $this->hasMany(Remark::class, 'taskid')->where('module_type', 'task-note-subnote')->whereNull('delete_at');
    }

    public function singleSubnotes(): HasOne
    {
        return $this->hasOne(Remark::class, 'taskid')->where('module_type', 'task-note-subnote')->whereNull('delete_at')->latest();
    }

    public function archiveSubnotes(): HasMany
    {
        return $this->hasMany(Remark::class, 'taskid')->where('module_type', 'task-note-subnote')->whereNotNull('delete_at');
    }
}
