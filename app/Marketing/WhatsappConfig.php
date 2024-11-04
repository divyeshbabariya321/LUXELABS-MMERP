<?php

namespace App\Marketing;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\ImQueue;
use App\Customer;
use Carbon\Carbon;
use App\MarketingMessageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class WhatsappConfig extends Model
{
    const WASSENGER = 'wassenger';

    protected $fillable = ['number', 'provider', 'username', 'password', 'is_customer_support', 'frequency', 'send_start', 'send_end', 'device_name', 'simcard_number', 'simcard_owner', 'payment', 'recharge_date', 'status', 'sim_card_type', 'instance_id', 'token', 'is_default', 'store_website_id', 'default_for', 'is_use_own'];

    public function customer(): HasMany
    {
        return $this->hasMany(Customer::class, 'broadcast_number', 'number');
    }

    public function customerAttachToday(): HasMany
    {
        return $this->hasMany(Customer::class, 'broadcast_number', 'number')->whereDate('updated_at', Carbon::today());
    }

    public function imQueueCurrentDateMessageSend(): HasMany
    {
        return $this->hasMany(ImQueue::class, 'number_from', 'number')->whereDate('sent_at', Carbon::today())->whereNotNull('sent_at');
    }

    public function imQueueLastMessageSend(): HasOne
    {
        return $this->hasOne(ImQueue::class, 'number_from', 'number')->latest();
    }

    public function imQueueLastMessagePending(): HasMany
    {
        return $this->hasMany(ImQueue::class, 'number_from', 'number')->whereNull('sent_at');
    }

    public function marketingMessageTypes(): HasOne
    {
        return $this->hasOne(MarketingMessageType::class, 'marketing_message_type_id', 'id');
    }

    public function getNumberCountInQueue($number, $startDate, $endDate = null)
    {
        if (!$endDate) {
            return ImQueue::where('number_from',$number)->whereDate('created_at', $startDate)->count();
        } else {
            return ImQueue::where('number_from',$number)->whereBetween('created_at', [$startDate,$endDate])->count();
        }
    }

    public static function getWhatsappConfigs()
    {
        try {
            $instances = self::select([
                'number', 'instance_id', 'provider', 'token', 'is_customer_support', 'is_default', 'is_use_own',
            ])
                ->whereNotNull('instance_id')
                ->whereNotNull('token')
                ->orderByDesc('is_default')
                ->get();

            if ($instances->isEmpty()) {
                return [];
            }

            $configs = [];
            foreach ($instances as $instance) {
                $formatted = [
                    'number' => $instance->number,
                    'instance_id' => $instance->instance_id,
                    'token' => $instance->token,
                    'customer_number' => $instance->is_customer_support == 1,
                    'is_use_own' => $instance->is_use_own,
                    'provider' => $instance->provider,
                ];
                if ($instance->is_default == 1) {
                    $configs[0] = $formatted;
                }
                $configs[$instance->number] = $formatted;
            }

            // Save to config dynamically if needed
            Config::set('apiwha.instances', $configs);

            return $configs;
        } catch (\Exception $e) {
            return [];
        }
    }
}
