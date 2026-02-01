<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\HasilAudit;

class ScanHasilAuditUploaded extends Mailable
{
    use Queueable, SerializesModels;

    public $hasilAudit;
    public $uploader;
    public $filePath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(HasilAudit $hasilAudit, $uploader, $filePath)
    {
        $this->hasilAudit = $hasilAudit;
        $this->uploader = $uploader;
        $this->filePath = $filePath;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: '✅ Scan Hasil Audit Berhasil Diupload - ' . $this->hasilAudit->kode_risiko,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.scan_hasil_audit_uploaded',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [
            Attachment::fromStorage($this->filePath)
                ->as($this->hasilAudit->file_scan)
                ->withMime('application/pdf'),
        ];
    }
}
