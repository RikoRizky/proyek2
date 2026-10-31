<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\BroadcastMail;
use Illuminate\Support\Facades\Log;

class SendBroadcastEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipientEmail;
    protected $subjectText;
    protected $messageContent;

    /**
     * Create a new job instance.
     */
    public function __construct($recipientEmail, $subjectText, $messageContent)
    {
        $this->recipientEmail = $recipientEmail;
        $this->subjectText = $subjectText;
        $this->messageContent = $messageContent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->recipientEmail)->send(new BroadcastMail($this->subjectText, $this->messageContent));
        } catch (\Exception $e) {
            Log::error("Failed to send broadcast email to {$this->recipientEmail}: " . $e->getMessage());
        }
    }
}
