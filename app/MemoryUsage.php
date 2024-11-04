<?php

namespace App;
use App\Http\Controllers\WhatsAppController;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemoryUsage extends Model
{
    use HasFactory;

    protected $table = 'memory_usage';

    public static function boot()
    {
        parent::boot();

        self::creating(function (MemoryUsage $memory) {
            $thresold_limit_for_memory_uses = Setting::where('name', 'thresold_limit_for_memory_uses')->first();

            $allUsers = User::get();

            $updatedData = $memory->getDirty();

            if (($updatedData['used'] / $updatedData['total']) * 100 > $thresold_limit_for_memory_uses->val) {
                foreach ($allUsers as $user) {
                    if ($user->isAdmin() && $user->phone && $user->whatsapp_number) {
                        app(Controllers\WhatsAppController::class)->sendWithWhatsApp($user->phone, $user->whatsapp_number, 'Uses of memory is increse from give limit of ' . $thresold_limit_for_memory_uses->val);
                    }
                }
            }
        });
    }
}
