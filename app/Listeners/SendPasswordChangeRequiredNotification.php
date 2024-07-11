<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Notifications\PasswordChangeRequiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendPasswordChangeRequiredNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        Notification::sendNow([$event->user], new PasswordChangeRequiredNotification());
    }
}
