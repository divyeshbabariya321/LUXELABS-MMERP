<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ScriptDocuments;

class ScriptsExecutionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'script_document_id',
        'description',
        'run_time',
        'run_output',
        'run_status',
    ];

    public function scriptDocument(): BelongsTo
    {
        return $this->belongsTo(ScriptDocuments::class, 'script_document_id');
    }
}
