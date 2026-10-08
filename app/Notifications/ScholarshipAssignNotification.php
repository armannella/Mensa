<?php

namespace App\Notifications;

use App\Enums\ScholarshipStatus;
use App\Models\DiscountPlan;
use App\Models\ScholarshipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScholarshipAssignNotification extends Notification
{
    use Queueable;
    public ScholarshipApplication $scholarship_application ;
    public ScholarshipStatus $scholarship_status;
    /**
     * Create a new notification instance.
     */
    public function __construct(ScholarshipApplication $scholarship_application , ScholarshipStatus $scholarship_status )
    {
        $this->scholarship_application = $scholarship_application;
        $this->scholarship_status = $scholarship_status;
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
        if($this->scholarship_status->value == ScholarshipStatus::APPROVED->value)
        {
            return [
                'title' => 'Scholarship Application Approved!',
                'message' => "After Reviewing your Request , you assigned to {{$this->scholarship_application->student->discountplan->name}} plan and your discount percentage is {{$this->scholarship_application->student->discountplan->percentage}}",
                'type' => 'success',
                'icon' => 'bi bi-card-checklist'
            ];
        }
        else
        {
            return [
                'title' => 'Scholarship Application Rejected!',
                'message' => "after reviewing your Request , your request rejected .",
                'type' => 'danger',
                'icon' => 'bi bi-calendar-x'
            ];
        }
    }
}
