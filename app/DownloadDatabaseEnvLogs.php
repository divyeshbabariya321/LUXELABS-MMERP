<?php

namespace App;
use App\User;
use App\StoreWebsite;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class DownloadDatabaseEnvLogs extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['store_website_id', 'user_id', 'type', 'cmd', 'output', 'return_var'];

    protected $appends = ['output_string'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'output' => 'array',
        ];
    }

    // $return_var === 0 - Command executed successfully
    // $return_var != 0 - Command failed to execute. Error code is returing in this varibale.
    public function saveLog($store_website_id, $user_id, $type, $cmd, $output = [], $return_var = null)
    {
        $this->store_website_id = $store_website_id;
        $this->user_id          = $user_id;
        $this->type             = $type;
        $this->cmd              = $cmd;
        $this->output           = $output;
        $this->return_var       = $return_var;
        $this->save();

        return $this; // Return the saved model instance
    }

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getOutputStringAttribute()
    {
        if (is_array($this->output)) {
            return json_encode($this->output);
        }

        return $this->output;
    }
}
