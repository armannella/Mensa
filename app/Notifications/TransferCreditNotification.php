<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferCreditNotification extends Notification
{
    use Queueable;

    public Student $sender ;
    public Student $reciever ;
    public float $amount ;
    /**
     * Create a new notification instance.
     */
    public function __construct(Student $sender , Student $reciever , float $amount)
    {
        $this->sender= $sender;
        $this->reciever = $reciever;
        $this->amount = $amount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
                'title' => 'Recieved Credit!',
                'message' => "{$this->sender->name} ({$this->sender->matricola}) sent you {$this->amount} Euro. ",
                'type' => 'success',
                'icon' => 'bi bi-currency-euro'
            ];
    }
}
