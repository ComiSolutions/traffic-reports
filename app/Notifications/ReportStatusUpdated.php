<?php

namespace App\Notifications;

use App\Models\TrafficReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public TrafficReport $report)
    {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->report->status?->value ?? 'updated';

        return (new MailMessage)
            ->subject('Traffic report status updated')
            ->greeting('Hello '.$notifiable->name)
            ->line("Your traffic offence report has been {$status}.")
            ->line('Report: '.$this->report->offence_type)
            ->action('View Report', route('reports.show', $this->report));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $status = $this->report->status?->value ?? 'updated';

        return [
            'traffic_report_id' => $this->report->id,
            'offence_type' => $this->report->offence_type,
            'status' => $status,
            'message' => "Your traffic offence report has been {$status}.",
            'url' => route('reports.show', $this->report),
        ];
    }
}
