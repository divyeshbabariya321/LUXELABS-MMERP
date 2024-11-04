<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserPemfileHistoryLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['user_pemfile_history_id', 'cmd', 'output', 'return_var'];

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
    public function saveLog($user_pemfile_history_id, $cmd, $output = [], $return_var = null)
    {
        $this->user_pemfile_history_id = $user_pemfile_history_id;
        $this->cmd                     = $cmd;
        $this->output                  = $output;
        $this->return_var              = $return_var;
        $this->save();
    }

    public function userPemfileHistory(): BelongsTo
    {
        return $this->belongsTo(UserPemfileHistory::class);
    }

    public function getOutputStringAttribute()
    {
        if (is_array($this->output)) {
            return json_encode($this->output);
        }

        return $this->output;
    }
}
