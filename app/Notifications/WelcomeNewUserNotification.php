<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class WelcomeNewUserNotification extends Notification
{
    protected $user;
    protected $rawPassword;

    public function __construct(User $user, string $rawPassword)
    {
        $this->user = $user;
        $this->rawPassword = $rawPassword;
    }

    // Send immediately via mail only (no queue, no database, etc.)
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . ' – Your Account is Ready!')
            ->greeting('Hello ' . $this->user->name . ',')
            ->line('Your account has been created successfully.')
            ->line('Here are your login credentials:')
            ->line('**Email:** ' . $this->user->email)
            ->line('**Temporary Password:** ' . $this->rawPassword)
            ->line('Please log in and change your password immediately.')
            ->action('Log in Now', url('/login'))
            ->line('If you have any issues, contact the IT department.')
            ->salutation('Best regards,');
    }
}