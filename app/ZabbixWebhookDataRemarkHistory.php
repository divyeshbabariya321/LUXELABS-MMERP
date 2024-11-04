<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ZabbixWebhookData;
use Illuminate\Database\Eloquent\Model;

class ZabbixWebhookDataRemarkHistory extends Model
{
    public $fillable = [
        'zabbix_webhook_data_id',
        'remarks',
        'user_id',
    ];

    public function zabbixWebhookData(): BelongsTo
    {
        return $this->belongsTo(ZabbixWebhookData::class, 'zabbix_webhook_data_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
