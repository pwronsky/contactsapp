<?php

namespace App\Contracts;

use App\Contact;
use Illuminate\Contracts\Auth\Authenticatable;

interface ContactRepository {
    public function paginate(Authenticatable $user, int $perPage = 6);
    public function findByEmailAndUser(string $email, Authenticatable $user): ?Contact;
}