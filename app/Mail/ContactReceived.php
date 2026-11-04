<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactReceived extends Mailable
{
    public function __construct(public ContactSubmission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Website enquiry from {$this->submission->name}",
            replyTo: [new Address($this->submission->email, $this->submission->name)],
        );
    }

    public function content(): Content
    {
        return new Content(text: 'mail.contact-received');
    }
}
