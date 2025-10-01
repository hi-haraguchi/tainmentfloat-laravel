<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordJa extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    $url = config('app.frontend_url')
        . '/password-reset/' . $this->token
        . '?email=' . urlencode($notifiable->email);

    return (new MailMessage)
        ->subject('パスワード再設定のご案内')
        ->greeting('再設定ありがとうございます！')
        
        ->line('パスワード再設定のリクエストを受け付けました。')
        ->action('パスワードを再設定する', $url)
        ->line('このリンクは60分で期限切れとなります。')
        ->line('もしこの操作に心当たりがない場合は、このメールは無視してください。')
        ->line('リンクがボタンから開けない場合は、以下のURLをコピーしてブラウザに貼り付けてください。')
        ->line($url)
        ->salutation('エンタメフロート 運営');
}

}
