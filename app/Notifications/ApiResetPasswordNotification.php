<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Lang;

class ApiResetPasswordNotification extends ResetPasswordNotification
{
    protected $uriChangePassword = '/change-password/';

    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        if (static::$createUrlCallback) {
            $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        } else {
            $url = url( $this->uriChangePassword . $this->token
                . '?email=' . $notifiable->getEmailForPasswordReset());
        }

        return (new MailMessage)
            ->subject(Lang::get('Уведомление о сбросе пароля!'))
            ->line(Lang::get('Вы получили это письмо, потому что мы получили запрос на сборс пароля для вашего аккаунта.'))
            ->action(Lang::get('СБРОС'), $url)
            ->line(Lang::get('Ссылка действительна в течении :count минут.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('Если вы не посылали запрос на сброс пароля, не предпринимайте никаких действий'));
    }
}
