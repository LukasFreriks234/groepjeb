<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FunctionEdited extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $function; 
    public function __construct($function)
    {
        $this->name=$function['name'];
        $this->Safety=$function['effects']['Safety'];
        $this->Recreation=$function['effects']['Recreation'];
        $this->Enviromental_Quality=$function['effects']['Environmental Quality'];
        $this->Services=$function['effects']['Services'];
        $this->Mobility=$function['effects']['Mobility'];
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line("The function " .$this->name ." has been edited. It's new effect values are:")
            ->line("Safety: ". $this->Safety."")
            ->line("Recreation: ". $this->Recreation."")
            ->line("Environmental Quality: ". $this->Enviromental_Quality."")
            ->line("Services: ". $this->Services."")
            ->line("Mobility: ". $this->Mobility."")
            ->action('To website', url('/'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
