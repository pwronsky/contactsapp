<?php

namespace App\Jobs;

use App\Contact;
use App\Integrations\Klaviyo\KlaviyoApiClient;
use App\Integrations\Klaviyo\Model\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrackEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $contact;

    /**
     * TrackEvent constructor.
     * @param Contact $contact
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @param KlaviyoApiClient $klaviyoApiClient
     */
    public function handle(KlaviyoApiClient $klaviyoApiClient)
    {
        $event = new Event(
            'Click Button',
            ['$email' => $this->contact->email],
            ['ContactId' => $this->contact->id]
        );

        $klaviyoApiClient->track($event);
    }
}
