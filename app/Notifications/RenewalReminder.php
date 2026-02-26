<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewalReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Subscription $subscription,
        public readonly int $daysLeft
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $planName  = $this->subscription->plan->name;
        $endDate   = $this->subscription->end_date->format('d/m/Y');
        $renewUrl  = route('subscription.index');

        return (new MailMessage)
            ->subject("⚠️ A sua subscrição {$planName} expira em {$this->daysLeft} dia(s)")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("A sua subscrição do plano **{$planName}** expira em **{$this->daysLeft} dia(s)** ({$endDate}).")
            ->line('Para continuar a usar todos os recursos sem interrupção, renove agora.')
            ->action('Renovar Subscrição', $renewUrl)
            ->line('Se já renovou, ignore este e-mail.')
            ->salutation('Equipa de Suporte');
    }
}
