<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Contact;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 3)->create()->each(function ($user) {
            $user->contacts()->save(factory(Contact::class)->make());
        });
    }
}
