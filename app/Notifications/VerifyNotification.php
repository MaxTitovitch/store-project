<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class VerifyNotification extends VerifyEmail
{
    protected $uriVerify = '/email-verify/';

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return (new MailMessage)
            ->subject(Lang::get('Подтверждение адреса электронной почты'))
            ->line(Lang::get('Нажмите эту кнопку чтобы подтвердить адрес электронной почты.'))
            ->action(Lang::get('ПОДТВЕРДИТЬ АДРЕС'), $verificationUrl)
            ->line(Lang::get('Если вы не посылали запрос на сброс пароля, не предпринимайте никаких действий'));
    }

    protected function verificationUrl($notifiable)
    {
         return $url = url( $this->uriVerify . '?token=' . sha1($notifiable->getEmailForVerification())
             . '&id=' . $notifiable->getKey());
    }
}
