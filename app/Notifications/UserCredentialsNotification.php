<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCredentialsNotification extends Notification
{
    use Queueable;



    public function __construct(protected $email, protected $password)
    {
        $this->onConnection('database');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Account Credentials')
            ->line('Here are your account credentials:')
            ->line("Email:  {$this->email}")
            ->line("Password:   {$this->password}")
            ->line('Please keep this information secure.')
            ->action('Log In', config('frontend_urls.signin')) // Provide a link to password reset
            ->line('We recommend changing your password once you log in.')
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            // Optionally define JSON payload
        ];
    }
}
