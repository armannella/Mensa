<?php

namespace App\Notifications;

use App\Models\Reserve;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AskForFeedbackNotification extends Notification
{
    use Queueable;

    public Reserve $reserve;

    public function __construct(Reserve $reserve)
    {
        $this->reserve = $reserve;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Enjoyed your meal?',
            'message' => "Your {" . $this->reserve->menu->meal->value . "} for {" . $this->reserve->menu->date->format('Y-m-d') . "} was delivered. Please go to your reserves and leave a feedback!",
            'type' => 'warning',
            'icon' => 'bi bi-star-fill'
        ];
    }
}