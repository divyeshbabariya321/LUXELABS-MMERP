<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MagentoCssVariable;
use Illuminate\Database\Eloquent\Model;

class MagentoCssVariableJobLog extends Model
{
    protected $fillable = [
        'magento_css_variable_id',
        'command',
        'message',
        'status',
        'csv_file_path',
    ];

    public function magentoCssVariable(): BelongsTo
    {
        return $this->belongsTo(MagentoCssVariable::class);
    }

    public function getFormattedMagentoCssVariableIdAttribute()
    {
        $magento_css_variable_id = $this->attributes['magento_css_variable_id'];

        if (strpos($magento_css_variable_id, ',') !== false) {
            $magentoIds = explode(',', $magento_css_variable_id);
            $result     = '';
            foreach ($magentoIds as $key => $magentoId) {
                $magento = MagentoCssVariable::find($magentoId);
                if ($magento && $magento->project) {
                    $result .= $magento->project->name . ', ';
                }
            }

            return rtrim($result, ', '); // Remove the trailing comma and space
        } else {
            $singleValue = $magento_css_variable_id;

            return $this->magentoCssVariable?->project?->name;
        }
    }
}
