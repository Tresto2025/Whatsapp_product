<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    public $token;
    public $user;

    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->user = $user; // Pass user object to access firstname/lastname
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    $resetUrl = url(route('password.reset', [
        'token' => $this->token,
        'email' => $notifiable->getEmailForPasswordReset(),
    ], false));

    return (new MailMessage)
        ->markdown('vendor.notifications.email', [
            'title' => $this->user->title ?? 'Mr./Ms.',  // optional, if you store title
            'firstname' => $this->user->first_name ?? '',
            'lastname' => $this->user->last_name ?? '',
            'introLines' => [
                'You are receiving this email because we received a password reset request for your account.',
            ],
            'actionText' => 'Reset Password',
            'actionUrl' => $resetUrl,
            'level' => 'primary',
            'outroLines' => [
                'If you did not request a password reset, no further action is required.',
            ],
        ]);
}
}
