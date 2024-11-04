<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThemeStructure extends Model
{
    protected $table = 'theme_structure';

    use HasFactory;

    public $fillable = [
        'theme_id',
        'name',
        'is_file',
        'parent_id',
        'position',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(ProjectTheme::class, 'theme_id', 'id');
    }

    public static function tree()
    {
        return static::orderByRaw('-position DESC')->get()->nest();
    }

    public static function treeList()
    {
        return static::orderByRaw('-position DESC')
            ->get()
            ->nest()
            ->setIndent('¦–– ')
            ->listsFlattened('name');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ThemeStructure::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ThemeStructure::class, 'parent_id')->with('children')->orderBy('position');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ThemeFile::class);
    }
}
