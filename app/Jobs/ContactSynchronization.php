<?php

namespace App\Jobs;

use App\Contact;
use App\Integrations\Klaviyo\KlaviyoApiClient;
use App\Integrations\Klaviyo\Model\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ContactSynchronization implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $contact;

    /**
     * ContactSynchronization constructor.
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
        $profile = new Profile(
            $this->contact->email,
            $this->contact->first_name,
            $this->contact->last_name ?: '',
            $this->contact->phone ?: ''
        );

        $klaviyoApiClient->identify($profile);
    }
}
