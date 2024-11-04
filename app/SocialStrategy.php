<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use App\ChatMessage;
use App\User;

class SocialStrategy extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="social_strategy_subject_id",type="integer")
     * @SWG\Property(property="execution_id",type="integer")
     * @SWG\Property(property="content_id",type="integer")
     * @SWG\Property(property="website_id",type="integer")
     */
    use Mediable;

    protected $fillable = ['social_strategy_subject_id', 'description', 'execution_id', 'content_id', 'website_id'];

    public function lastChat(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'social_strategy_id', 'id')->orderByDesc('created_at')->latest();
    }

    public function whatsappAll($needBroadcast = false): HasMany
    {
        if ($needBroadcast) {
            return $this->hasMany(ChatMessage::class, 'social_strategy_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(ChatMessage::class, 'social_strategy_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    public function content(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'content_id');
    }

    public function execution(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'execution_id');
    }
}
