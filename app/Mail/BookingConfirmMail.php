<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingConfirmMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $bookingDetails;

    public function __construct(array $bookingDetails)
    {
        $this->bookingDetails = $bookingDetails;
    }

    public function envelope(): Envelope
    {
        $subject = sprintf(
            'Xác nhận đặt vé #%s - %s đi %s ngày %s',
            $this->bookingDetails['booking_code'] ?? 'Mới',
            $this->bookingDetails['start_province'] ?? 'N/A',
            $this->bookingDetails['end_province'] ?? 'N/A',
            $this->bookingDetails['departure_date'] ?? 'N/A'
        );

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking_confirm',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job gửi mail thất bại', [
            'booking_code' => $this->bookingDetails['booking_code'] ?? 'N/A',
            'error' => $exception->getMessage()
        ]);
    }
}
