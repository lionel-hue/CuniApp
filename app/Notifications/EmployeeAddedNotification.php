<?php
// app/Notifications/EmployeeAddedNotification.php
namespace App\Notifications;

use App\Models\User;
use App\Models\Firm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $employee;
    public Firm $firm;

    public function __construct(User $employee, Firm $firm)
    {
        $this->employee = $employee;
        $this->firm = $firm;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Bienvenue dans ' . $this->firm->name)
            ->greeting('Bonjour ' . $this->employee->name . ',')
            ->line('Vous avez été ajouté comme employé à l\'entreprise ' . $this->firm->name . '.')
            ->line('Votre administrateur vous a créé un compte.')
            ->action('Se connecter à CuniApp', url('/login'))
            ->line('Email: ' . $this->employee->email)
            ->line('Pour votre sécurité, veuillez changer votre mot de passe après la première connexion.')
            ->salutation('L\'équipe CuniApp Élevage');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'success',
            'title' => '🎉 Bienvenue dans ' . $this->firm->name,
            'message' => 'Vous avez été ajouté comme employé. Connectez-vous pour commencer.',
            'action_url' => route('dashboard'),
            'firm_id' => $this->firm->id,
        ];
    }
}
