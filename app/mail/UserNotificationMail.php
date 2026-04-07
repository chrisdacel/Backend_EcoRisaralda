<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $messageText;
    public string $actionUrl;
    public string $actionLabel;

    public function __construct(string $subjectLine, string $messageText, string $actionUrl, string $actionLabel)
    {
        $this->subjectLine = $subjectLine;
        $this->messageText = $messageText;
        $this->actionUrl = $actionUrl;
        $this->actionLabel = $actionLabel;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user_notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
