<?php

namespace App\Notifications;

use App\Models\Build;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuildStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Build $build;

    /**
     * Create a new notification instance.
     */
    public function __construct(Build $build)
    {
        $this->build = $build;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function channels(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $app = $this->build->app;
        $status = ucfirst($this->build->build_status);

        $message = (new MailMessage)
            ->subject("Build #{$this->build->id} for {$app->app_name} is {$status}")
            ->line("Your app build has status: **{$status}**.");

        if ($this->build->build_status === 'completed') {
            $message->line("You can now download the generated files.")
                ->action('Go to Dashboard', url('/user/apps'));
        } else {
            $message->line("The build failed. Please check the logs in your dashboard.")
                ->action('Check Build Logs', url('/user/apps'));
        }

        return $message;
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $app = $this->build->app;
        return [
            'build_id' => $this->build->id,
            'app_name' => $app->app_name,
            'build_status' => $this->build->build_status,
            'message' => "Build #{$this->build->id} for {$app->app_name} has {$this->build->build_status} successfully.",
        ];
    }
}
