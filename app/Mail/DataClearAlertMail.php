<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DataClearAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $clearDate,
        public int $year,
    ) {}

    public function build()
    {
        return $this->subject("⚠️ Peringatan — Data tahun {$this->year} akan dihapus pada {$this->clearDate}")
                    ->view('Email.dataClearAlert');
    }
}
