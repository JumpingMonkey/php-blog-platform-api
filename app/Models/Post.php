<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Post",
 *     title="Post",
 *     description="Blog post model"
 * )
 */
class Post extends Model
{
    use HasFactory;

    /**
     * @OA\Property(property="id", type="integer", format="int64", example=1)
     * @OA\Property(property="title", type="string", example="My First Blog Post")
     * @OA\Property(property="content", type="string", example="This is the content of my first blog post.")
     * @OA\Property(property="category", type="string", example="Technology")
     * @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"Tech", "Programming"})
     * @OA\Property(property="created_at", type="string", format="datetime", example="2025-02-23T12:00:00Z")
     * @OA\Property(property="updated_at", type="string", format="datetime", example="2025-02-23T12:00:00Z")
     */
    protected $fillable = [
        'title',
        'content',
        'category',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}

/**
 * @OA\Schema(
 *     schema="PostRequest",
 *     title="Post Request",
 *     description="Post request body"
 * )
 */
class PostRequest
{
    /**
     * @OA\Property(property="title", type="string", example="My First Blog Post")
     * @OA\Property(property="content", type="string", example="This is the content of my first blog post.")
     * @OA\Property(property="category", type="string", example="Technology")
     * @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"Tech", "Programming"})
     */
}
