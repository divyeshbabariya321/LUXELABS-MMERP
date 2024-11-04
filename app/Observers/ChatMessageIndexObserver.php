<?php

namespace App\Observers;

use App\ChatMessage;
use App\Elasticsearch\Reindex\Messages;

class ChatMessageIndexObserver
{
    public function created(ChatMessage $chatMessage): void
    {
        // Perform a full reindex when a new message is created
        $message = new Messages;
        $message->partialReindex($chatMessage);
    }

    public function updated(ChatMessage $chatMessage): void
    {
        // Only perform a partial reindex if certain fields have changed
        $updateMessage = new Messages;
        $updateMessage->partialReindex($chatMessage);
    }
}
