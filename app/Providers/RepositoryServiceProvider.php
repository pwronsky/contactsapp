<?php

namespace App\Providers;

use App\Contracts\ContactRepository;
use App\Repositories\EloquentContactRepository;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(ContactRepository::class, EloquentContactRepository::class);
    }
}
