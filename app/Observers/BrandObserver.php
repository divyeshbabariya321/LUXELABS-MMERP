<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Brand;

class BrandObserver
{
    /**
     * Handle the brand "created" event.
     */
    public function created(Brand $brand): void
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
    }

    /**
     * Handle the brand "updated" event.
     */
    public function updated(Brand $brand): void
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
    }

    /**
     * Handle the brand "deleted" event.
     */
    public function deleted(Brand $brand): void
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
    }

    /**
     * Handle the brand "restored" event.
     */
    public function restored(Brand $brand): void
    {
        //
    }

    /**
     * Handle the brand "force deleted" event.
     */
    public function forceDeleted(Brand $brand): void
    {
        //
    }
}
