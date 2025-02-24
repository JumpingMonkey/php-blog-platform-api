<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private array $validPostData = [
        'title' => 'Test Post',
        'content' => 'This is a test post content',
        'category' => 'Testing',
        'tags' => ['Test', 'PHPUnit']
    ];

    public function test_can_create_post(): void
    {
        $response = $this->postJson('/api/v1/posts', $this->validPostData);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Post',
                'content' => 'This is a test post content',
                'category' => 'Testing',
                'tags' => ['Test', 'PHPUnit']
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'category' => 'Testing'
        ]);
    }

    public function test_cannot_create_post_with_invalid_data(): void
    {
        $invalidData = [
            'title' => '',
            'content' => '',
            'category' => '',
            'tags' => 'not-an-array'
        ];

        $response = $this->postJson('/api/v1/posts', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'category', 'tags'])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'title',
                    'content',
                    'category',
                    'tags'
                ]
            ]);
    }

    public function test_can_get_all_posts_with_pagination(): void
    {
        Post::create($this->validPostData);
        Post::create([
            'title' => 'Second Post',
            'content' => 'This is another test post',
            'category' => 'Testing',
            'tags' => ['Test']
        ]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'category',
                        'tags',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
    }

    public function test_can_get_single_post(): void
    {
        $post = Post::create($this->validPostData);

        $response = $this->getJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Test Post',
                'content' => 'This is a test post content',
                'category' => 'Testing',
                'tags' => ['Test', 'PHPUnit']
            ]);
    }

    public function test_returns_404_for_non_existent_post(): void
    {
        $response = $this->getJson('/api/v1/posts/999');

        $response->assertStatus(404)
            ->assertExactJson([
                'message' => 'Resource not found.'
            ]);
    }

    public function test_can_update_post(): void
    {
        $post = Post::create($this->validPostData);

        $updatedData = [
            'title' => 'Updated Post',
            'content' => 'This is updated content',
            'category' => 'Updated Category',
            'tags' => ['Updated', 'Test']
        ];

        $response = $this->putJson("/api/v1/posts/{$post->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Post',
                'content' => 'This is updated content',
                'category' => 'Updated Category',
                'tags' => ['Updated', 'Test']
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Post',
            'category' => 'Updated Category'
        ]);
    }

    public function test_cannot_update_post_with_invalid_data(): void
    {
        $post = Post::create($this->validPostData);

        $invalidData = [
            'title' => '',
            'content' => '',
            'category' => '',
            'tags' => 'not-an-array'
        ];

        $response = $this->putJson("/api/v1/posts/{$post->id}", $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'category', 'tags'])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'title',
                    'content',
                    'category',
                    'tags'
                ]
            ]);
    }

    public function test_can_delete_post(): void
    {
        $post = Post::create($this->validPostData);

        $response = $this->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_can_search_posts(): void
    {
        Post::create([
            'title' => 'PHP Testing',
            'content' => 'Content about testing',
            'category' => 'Programming',
            'tags' => ['PHP']
        ]);

        Post::create([
            'title' => 'Laravel Guide',
            'content' => 'Guide about Laravel',
            'category' => 'Framework',
            'tags' => ['Laravel']
        ]);

        $response = $this->getJson('/api/v1/posts?term=testing');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.title', 'PHP Testing')
            ->assertJsonCount(1, 'data');
    }

    public function test_rate_limiting(): void
    {
        for ($i = 0; $i < 61; $i++) {
            $response = $this->getJson('/api/v1/posts');
            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429)
                    ->assertJsonStructure(['message']);
            }
        }
    }
}
