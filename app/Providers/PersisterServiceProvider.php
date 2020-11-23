<?php

namespace App\Providers;

use App\Contracts\ContactPersister;
use App\Persistence\EloquentContactPersister;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class PersisterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(ContactPersister::class, EloquentContactPersister::class);
    }
}
