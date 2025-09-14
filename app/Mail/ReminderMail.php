<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Title;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $lastTitle;
    public $days;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, ?Title $lastTitle = null, int $days = 14)
    {
        $this->user = $user->only(['id', 'email']);
        $this->lastTitle = $lastTitle;
        $this->days = $days;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'エンタメのリマインド',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder',
            with: [
                'user'      => $this->user,
                'lastTitle' => $this->lastTitle,
                'days'      => $this->days,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
