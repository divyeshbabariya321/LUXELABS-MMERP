<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SonarQube extends Model
{
    use HasFactory;

    public $fillable = [
        'key',
        'rule',
        'severity',
        'component',
        'project',
        'hash',
        'textRange',
        'flows',
        'resolution',
        'status',
        'message',
        'effort',
        'debt',
        'author',
        'tags',
        'creationDate',
        'updateDate',
        'closeDate',
        'type',
        'scope',
        'quickFixAvailable',
        'messageFormattings',
        'codeVariants',
    ];

    public static function getFilterSeverity()
    {
        return SonarQube::select('severity')->where('severity', '!=', '')->groupBy('severity')->pluck('severity', 'severity');
    }

    public static function getFilterAuthor()
    {
        return SonarQube::select('author')->where('author', '!=', '')->groupBy('author')->pluck('author', 'author');
    }

    public static function getFilterProject()
    {
        return SonarQube::select('project')->where('project', '!=', '')->groupBy('project')->pluck('project', 'project');
    }

    public static function getFilterStatus()
    {
        return SonarQube::select('status')->where('status', '!=', '')->groupBy('status')->pluck('status', 'status');
    }
}
