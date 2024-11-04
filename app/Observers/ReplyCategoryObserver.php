<?php

namespace App\Observers;

use App\ReplyCategory;
use Illuminate\Support\Facades\Cache;

class ReplyCategoryObserver
{
    /**
     * Handle the ReplyCategory "created" event.
     */
    public function created(ReplyCategory $replyCategory): void
    {
        $this->storeCacheReplyCategory();
    }

    /**
     * Handle the ReplyCategory "updated" event.
     */
    public function updated(ReplyCategory $replyCategory): void
    {
        $this->storeCacheReplyCategory();
    }

    /**
     * Handle the ReplyCategory "deleted" event.
     */
    public function deleted(ReplyCategory $replyCategory): void
    {
        //
    }

    /**
     * Handle the ReplyCategory "restored" event.
     */
    public function restored(ReplyCategory $replyCategory): void
    {
        //
    }

    /**
     * Handle the ReplyCategory "force deleted" event.
     */
    public function forceDeleted(ReplyCategory $replyCategory): void
    {
        //
    }

    public function storeCacheReplyCategory()
    {
        Cache::forget('reply_categories');
        Cache::put('reply_categories', ReplyCategory::select('id', 'name')->with('approval_leads')->orderBy('name', 'ASC')->get(), 60 * 60 * 24 * 1);

        Cache::forget('reply_parent_category');
        Cache::put('reply_parent_category', ReplyCategory::select('id', 'name')->with('approval_leads', 'sub_categories')->where('parent_id', 0)->orderby('name', 'ASC')->get(), 60 * 60 * 24 * 1);
    }
}
