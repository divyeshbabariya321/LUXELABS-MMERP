<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\User;


class Notification extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="role",type="string")

     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="sent_to",type="integer")
     * @SWG\Property(property="sale_id",type="integer")
     * @SWG\Property(property="task_id",type="integer")
     * @SWG\Property(property="message_id",type="integer")
     * @SWG\Property(property="reminder",type="string")
     */
    protected $fillable = [
        'message',
        'role',
        'product_id',
        'user_id',
        'sent_to',
        'sale_id',
        'task_id',
        'message_id',
        'reminder',
    ];

    public static function getUserNotificationByRoles($limit = 10)
    {
        $notifications = self::select('notifications.message', 'notifications.isread', 'notifications.id', 'notifications.product_id', 'notifications.sale_id', 'p.sku', 'p.name as pname', 'u.name as uname', 'notifications.created_at')
                            ->whereIn('role', Auth::user()->getRoleNames())
                            ->orWhere('sent_to', Auth::id())
                            ->latest('notifications.created_at')
                            ->limit($limit)
                            ->leftJoin('products as p', 'notifications.product_id', '=', 'p.id')
                            ->leftJoin('users as u', 'notifications.user_id', '=', 'u.id')
                            ->get();

        return $notifications;
    }

    public static function getUserNotificationByRolesPaginate(Request $request)
    {
        $orderBy   = 'n.created_at';
        $direction = 'desc';

        if ($request->has('sort_by')) {
            if ($request->input('sort_by') == 'by_user') {
                $orderBy   = 'n.user_id';
                $direction = 'asc';
            }

            if ($request->input('sort_by') == 'by_task') {
                $orderBy   = 'n.role';
                $direction = 'asc';
            }
        }

        $notifications = self::select('notifications.message', 'notifications.isread', 'notifications.id', 'notifications.product_id', 'notifications.sale_id', 'p.sku', 'p.name as pname', 'u.name as uname', 'notifications.created_at')
                            ->whereIn('role', Auth::user()->getRoleNames())
                            ->orWhere('sent_to', Auth::id())
                            ->leftJoin('products as p', 'notifications.product_id', '=', 'p.id')
                            ->leftJoin('users as u', 'notifications.user_id', '=', 'u.id')
                            ->orderBy($orderBy, $direction)
                            ->paginate(20);

        return $notifications;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo('Spatie\Permission\Models\Role', 'role', 'name');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getAll()
    {
        return self::all();
    }
}
