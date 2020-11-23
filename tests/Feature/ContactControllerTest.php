<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function get_contacts_as_unauthenticated_user_should_fail()
    {
        $this->expectException(AuthenticationException::class);
        $this->get('/api/contacts')->json();
    }

    /**
     * @test
     */
    public function newly_created_user_should_have_zero_contacts()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get('/api/contacts')
            ->json();

        $this->assertCount(0, $response['data']);
    }

    /**
     * @test
     */
    public function user_should_not_be_able_to_add_empty_contact()
    {
        $user = factory(User::class)->create();

        $this->expectException(ValidationException::class);

        $this->actingAs($user)
            ->post('/api/contacts', [])
            ->json();
    }

    /**
     * @test
     */
    public function user_should_be_able_to_add_new_contact()
    {
        $this->withoutJobs();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post('/api/contacts', ["first_name" => 'test', "email" => 'test@test.com'])
            ->json();

        $response = $this->actingAs($user)
            ->get('/api/contacts')
            ->json();

        $this->assertCount(1, $response['data']);
    }

    /**
     * @test
     */
    public function user_should_be_able_to_remove_contact_he_created()
    {
        $this->withoutJobs();
        $user = factory(User::class)->create();

        $data = $this->actingAs($user)
            ->post('/api/contacts', ["first_name" => 'test', "email" => 'test@test.com'])
            ->json();

        $this->assertDatabaseHas('contacts', $data);

        $response = $this->actingAs($user)
            ->delete('/api/contacts/' . $data['id']);

        $this->assertDatabaseMissing('contacts', $data);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
    }

    /**
     * @test
     */
    public function user_should_not_be_able_to_remove_contact_which_does_not_belong_to_him()
    {
        $this->withoutJobs();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $addContactResponse = $this->actingAs($user1)
            ->post('/api/contacts', ["first_name" => 'test', "email" => 'test@test.com'])
            ->json();

        $this->assertDatabaseHas('contacts', $addContactResponse);

        $removeContactResponse = $this->actingAs($user2)
            ->delete('/api/contacts/' . $addContactResponse['id']);

        $this->assertDatabaseHas('contacts', $addContactResponse);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $removeContactResponse->status());
    }

    /**
     * @test
     */
    public function user_should_be_able_to_update_contact_data()
    {
        $this->withoutJobs();
        $user = factory(User::class)->create();

        $newContact = $this->actingAs($user)
            ->post('/api/contacts', ["first_name" => 'test', "email" => 'test@test.com'])
            ->json();

        $this->assertDatabaseHas('contacts', ["first_name" => 'test']);

        $this->actingAs($user)
            ->put('/api/contacts/' . $newContact['id'], ["first_name" => 'updated first name', "email" => 'test@test.com'])
            ->json();

        $this->assertDatabaseMissing('contacts', ["first_name" => 'test']);
        $this->assertDatabaseHas('contacts', ["first_name" => 'updated first name']);
    }
}
