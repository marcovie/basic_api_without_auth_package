<?php


use App\Models\User;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiQuoteTest extends TestCase
{
    use DatabaseTransactions,WithFaker;

    /**
     * @var User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    private $user;

    /**
     * @return void
     */
    public function test_getting_quotes_with_no_user_return_failed_response(): void {
        $this->json('get', route('quote'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(['success' => false]);
    }

    /**
     * @return void
     */
    public function test_getting_quotes_with_a_user_return_successful_response(): void {
        $this->user = User::factory()->create();

        $this->actingAs($this->user, 'api');

        $this->json('get', route('quote'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['success' => true]);
    }
}
