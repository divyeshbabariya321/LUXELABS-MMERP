<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 10/08/18
 * Time: 8:04 PM
 */

namespace App\Http\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\NotificaitonContoller;

class NotificationComposer
{
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function compose(View $view)
    {
        $view->with('notifications', NotificaitonContoller::json($this->auth))->with('users', NotificaitonContoller::users($this->auth));
    }
}
