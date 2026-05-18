<?php

namespace App\Listeners;

use App\Events\ContactScoreProcessed;
use Illuminate\Support\Facades\Log;

class LogContactScore
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ContactScoreProcessed $event): void
    {
        $contact = $event->contact;
        
        Log::channel('contact')->info('Contact Score Processed', [
            'id' => $contact->id,
            'email' => $contact->email,
            'score' => $contact->score,
            'status' => $contact->status,
        ]);
    }
}
