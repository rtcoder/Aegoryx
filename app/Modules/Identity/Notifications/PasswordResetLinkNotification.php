<?php

namespace App\Modules\Identity\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PasswordResetLinkNotification extends Notification
{
    public function __construct(
        private readonly string $url,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('common.password_reset_mail_subject'))
            ->greeting(__('common.password_reset_mail_greeting'))
            ->line(__('common.password_reset_mail_line'))
            ->action(__('common.password_reset_mail_action'), $this->url)
            ->line(__('common.password_reset_mail_expiry', [
                'minutes' => (string) config('auth.passwords.users.expire', 60),
            ]))
            ->line(__('common.password_reset_mail_ignore'));
    }

    /**
     * @return array{url: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'url' => $this->url,
        ];
    }
}
