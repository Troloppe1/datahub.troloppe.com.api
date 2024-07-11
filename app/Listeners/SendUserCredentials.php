<?php

namespace App\Listeners;

use App\Events\CreatingUser;
use App\Notifications\PasswordChangeRequiredNotification;
use App\Notifications\UserCredentialsNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendUserCredentials
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
    public function handle(CreatingUser $event): void
    {
        $email = $event->user->email;
        $password = $event->user->password;
        Notification::send([$event->user], new UserCredentialsNotification($email, $password));

    }
}
