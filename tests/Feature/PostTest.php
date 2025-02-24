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
        $response = $this->postJson('/api/posts', $this->validPostData);

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

        $response = $this->postJson('/api/posts', $invalidData);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['title', 'content', 'category', 'tags']);
    }

    public function test_can_get_all_posts(): void
    {
        Post::create($this->validPostData);
        Post::create([
            'title' => 'Second Post',
            'content' => 'This is another test post',
            'category' => 'Testing',
            'tags' => ['Test']
        ]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'content',
                    'category',
                    'tags',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_can_get_single_post(): void
    {
        $post = Post::create($this->validPostData);

        $response = $this->getJson("/api/posts/{$post->id}");

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
        $response = $this->getJson('/api/posts/999');

        $response->assertStatus(404);
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

        $response = $this->putJson("/api/posts/{$post->id}", $updatedData);

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

    public function test_can_delete_post(): void
    {
        $post = Post::create($this->validPostData);

        $response = $this->deleteJson("/api/posts/{$post->id}");

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

        $response = $this->getJson('/api/posts?term=testing');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                [
                    'title' => 'PHP Testing'
                ]
            ]);
    }
}
