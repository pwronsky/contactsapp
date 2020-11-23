<?php

namespace App\Persistence;

use App\Jobs\ContactSynchronization;
use Exception;
use App\Contact;
use App\Contracts\ContactPersister;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Bus\Dispatcher;

class EloquentContactPersister implements ContactPersister {
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Contact $contact
     * @throws Exception
     */
    public function remove(Contact $contact)
    {
        $contact->delete();
    }

    /**
     * @param array $data
     * @param Authenticatable $user
     * @return Contact
     */
    public function create(array $data, Authenticatable $user): Contact
    {
        $contact = new Contact($data);
        $contact->user_id = $user->getAuthIdentifier();
        $contact->save();

        $this->dispatcher->dispatch(new ContactSynchronization($contact));

        return $contact;
    }

    /**
     * @param array $data
     * @param Contact $contact
     * @return Contact
     */
    public function update(array $data, Contact $contact): Contact
    {
        $contact->fill($data);
        $contact->save();

        $this->dispatcher->dispatch(new ContactSynchronization($contact));

        return $contact;
    }
}