<?php

namespace Modules\Api\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('Verify Email Address'))
            ->view('emails.verification', [
                'url' => $this->verificationUrl($notifiable),
            ]);
    }

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'api.v1.email.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
