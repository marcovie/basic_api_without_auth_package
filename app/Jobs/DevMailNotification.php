<?php

namespace App\Jobs;

use App\Mail\ErrorAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DevMailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $exception = [];

    /**
     * Create a new job instance.
     */
    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send Mail
        \Log::error($this->exception);
        Mail::to(env('APP_DEV_EMAIL', 'support@companyDomain.com'))->send(new ErrorAlert($this->exception));//Uses developer email found in .env or default company email.
    }
}
