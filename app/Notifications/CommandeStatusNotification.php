<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommandeStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Commande $commande)
    {
        //
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
        [$sujet, $ligne] = match ($this->commande->status) {
            'pending' => [
                'Votre commande est en attente',
                'Votre commande a bien été reçue et est en cours de traitement.',
            ],
            'approved' => [
                'Votre commande a été approuvée',
                'Bonne nouvelle ! Votre commande a été approuvée.',
            ],
            'cancelled' => [
                'Votre commande a été annulée',
                'Votre commande a malheureusement été annulée.',
            ],
            default => ['Mise à jour de votre commande', 'Statut mis à jour.'],
        };

        // ✅ Libellé statut paiement avec avance
        $statutPaiement = match ($this->commande->payment_status) {
            'payée' => 'Entièrement payée',
            'avance' => 'Avance / Partiellement payée',
            'nonpayé' => 'Non payée',
            default => $this->commande->payment_status,
        };

        $methode = match ($this->commande->payment_method) {
            'cash' => 'Espèces (Cash)',
            'mobile_money' => 'Mobile Money',
            'virement' => 'Virement bancaire',
            'carte' => 'Carte bancaire',
            default => $this->commande->payment_method,
        };

        return (new MailMessage)
            ->subject($sujet)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($ligne)
            ->line('---')
            ->line('**Référence :** ' . $this->commande->numero_commande)
            ->line('**Produit :** ' . $this->commande->nom_produit)
            ->line('**Total :** ' . number_format($this->commande->total_payements, 2) . ' Ar')
            ->line('**Montant payé :** ' . number_format($this->commande->montant_paye, 2) . ' Ar')
            ->line('**Reste à payer :** ' . number_format($this->commande->reste_a_payer, 2) . ' Ar')
            ->line('**Statut paiement :** ' . $statutPaiement)
            ->line('**Mode paiement :** ' . $methode)
            ->action('Voir mes commandes', route('commande.mecommande'))
            ->line('Merci de nous faire confiance !'
        );

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
