<?php

namespace App\Policies;

use App\Contact;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    public const MODIFICATION = 'modification';

    use HandlesAuthorization;

    public function modification(User $user, Contact $contact): bool
    {
        return (int) $user->id === (int) $contact->user_id;
    }
}
