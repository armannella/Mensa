<?php

namespace App\Notifications;

use App\Models\Food;
use App\Models\Menu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaitListNotification extends Notification
{
    use Queueable;
    public Food $food;
    public Menu $menu;
    public string $status;

    public function __construct(Menu $menu , Food $food, string $status)
    {
        $this->food = $food;
        $this->menu = $menu;
        $this->status = $status;
    }

    
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        if ($this->status === 'success') {
            return [
                'title' => 'Waitlist Success!',
                'message' => "A capacity for {$this->food->name} on {$this->menu->meal->value} of {$this->menu->date->format('Y-m-d')} became available and was automatically reserved for you.",
                'type' => 'success',
                'icon' => 'bi bi-check-circle'
            ];
        }

        return [
            'title' => 'Waitlist Failed!',
            'message' => "It was your turn for {$this->food->name} on {$this->menu->meal->value} of {$this->menu->date->format('Y-m-d')} , but your wallet didn't have enough balance. The reservation was passed to the next person.",
            'type' => 'danger',
            'icon' => 'bi bi-wallet2'
        ];
    }
}