<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PlaceEvent;
use App\Models\TuristicPlace;

class NewEventNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $place;
    public $user;

    public function __construct(PlaceEvent $event, TuristicPlace $place, $user)
    {
        $this->event = $event;
        $this->place = $place;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('¡Nuevo evento en uno de tus sitios favoritos!')
            ->markdown('emails.new_event_notification');
    }
}
