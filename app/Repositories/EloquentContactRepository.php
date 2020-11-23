<?php

namespace App\Repositories;

use App\Contracts\ContactRepository;
use App\Contact;
use Illuminate\Contracts\Auth\Authenticatable;

class EloquentContactRepository implements ContactRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Contact();
    }

    /**
     * @param Authenticatable $user
     * @param int $perPage
     * @return mixed
     */
    public function paginate(Authenticatable $user, int $perPage = 6)
    {
        return $this->model
            ->where('user_id', $user->getAuthIdentifier())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByEmailAndUser(string $email, Authenticatable $user): ?Contact
    {
        return $this->model
            ->where('email', $email)
            ->where('user_id', $user->getAuthIdentifier())
            ->first();
    }
}