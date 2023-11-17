<?php

namespace Tests\Feature;

use App\Models\User;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use DatabaseTransactions,WithFaker;

    /**
     * @var User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    private $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user=User::factory()->create();

        $this->actingAs($this->user);
    }

    /**
     * @return void
     */
    public function test_must_enter_email_and_password_returns_a_failed_response(): void {
        $this->json('POST', route('login'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['success' => false])
            ->assertJsonStructure(["success", "message", "data"]);
    }

    /**
     * @return void
     */
    public function test_user_auth_with_wrong_info_returns_a_failed_response(): void {
        $post_data = [
            'email'     => $this->user->email,
            'password'  => 'wrong_password',
        ];

        $this->json('POST', route('login'), $post_data)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(['success' => false]);
    }

    /**
     * @return void
     */
    public function test_user_auth_with_a_seeded_user_returns_a_successful_response(): void
    {
        $post_data = [
            'email'     => $this->user->email,
            'password'  => 'password',
        ];

        $this->json('POST', route('login'), $post_data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['success' => true])
            ->assertJsonStructure(["success", "message", "data"]);

        $this->assertAuthenticated();
    }

    /**
     * @return void
     */
    public function test_user_auth_logout()
    {
        $this->actingAs($this->user, 'api');

        $this->post(route('logout'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['success' => true]);
    }
}
