<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\User;
use App\Models\Story;
use Illuminate\Support\Facades\Log;

class StoryAPITest extends TestCase
{
    /**
     * A basic test example.
     */
    // public function test_the_application_returns_a_successful_response(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and act as that user for API requests
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function testCreateStory()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $postData = [
            'title' => 'Test Story',
            'content' => 'This is a test story content.',
            'status' => 'Published',
        ];

        $postData['created_by'] = $user->id;

        $response = $this->postJson('/api/stories', $postData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'title' => $postData['title'],
                'content' => $postData['content'],
                'status' => $postData['status'],
                'created_by' => $postData['created_by'],
            ])
            ->assertJsonStructure([
                "success",
                "data" => [
                    "id",
                    "title",
                    "content",
                    "status",
                    "created_by",
                    "created_at",
                    "updated_at",
                ],
                "message",
            ]);

        $this->assertDatabaseHas('stories', $postData);
    }

    public function testReadStory()
    {
        $story = Story::factory()->create();

        $response = $this->getJson('/api/stories/' . $story->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => $story->title,
                'content' => $story->content,
                'status' => $story->status,
                'created_by' => $story->created_by
            ])
            ->assertJsonStructure([
                "success",
                "data" => [
                    "id",
                    "title",
                    "content",
                    "status",
                    "created_by",
                    "created_at",
                    "updated_at",
                ],
                "message",
            ]);
    }

    public function testUpdateStory()
    {
        $story = Story::factory()->create();

        $updatedData = [
            'title' => 'Updated Story',
            'content' => 'This story has been updated.',
            'status' => 'Published',
            'created_by' => 'user'
        ];

        $response = $this->putJson("/api/stories/{$story->id}", $updatedData);

        // $response->assertStatus(200);
        // $this->assertDatabaseHas('stories', $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $story->id,
                'title' => $updatedData['title'],
                'content' => $updatedData['content'],
                'status' => $updatedData['status'],
                'created_by' => $updatedData['created_by']
            ]);

        $this->assertDatabaseHas('stories', $updatedData);
    }

    public function testDeleteStory()
    {
        $story = Story::factory()->create();

        $response = $this->deleteJson('/api/stories/' . $story->id);

        // $response->assertStatus(200);
        // $this->assertDeleted('stories', $story->toArray());

        $response->assertStatus(200);

        $this->assertDatabaseMissing('stories', ['id' => $story->id]);
    }

}
