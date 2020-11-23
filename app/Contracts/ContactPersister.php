<?php

namespace App\Contracts;

use App\Contact;
use Illuminate\Contracts\Auth\Authenticatable;

interface ContactPersister {
    public function remove(Contact $contact);
    public function create(array $data, Authenticatable $user): Contact;
    public function update(array $data, Contact $contact): Contact;
}