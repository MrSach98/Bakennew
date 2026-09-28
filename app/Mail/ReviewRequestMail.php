<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequestMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->load('items');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'How was your order? Share your review - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-request',
            with: ['order' => $this->order],
        );
    }
}