<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CodeShortcutFolder;
use Illuminate\Database\Eloquent\Model;

class CodeShortcut extends Model
{

    protected $fillable = [
        'user_id',
        'supplier_id',
        'code',
        'description',
        'code_shortcuts_platform_id',
        'title',
        'solution',
    ];

    public function user_detail(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function supplier_detail(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function platform(): HasOne
    {
        return $this->hasOne(CodeShortCutPlatform::class, 'id', 'code_shortcuts_platform_id');
    }

    public function folder(): HasOne
    {
        return $this->hasOne(CodeShortcutFolder::class, 'id', 'folder_id');
    }
}
