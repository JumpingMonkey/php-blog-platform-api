<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Blog Platform API Documentation",
 *     description="API endpoints for managing blog posts"
 * )
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/posts",
     *     summary="Get all posts",
     *     description="Retrieve all blog posts, optionally filtered by search term",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="term",
     *         in="query",
     *         description="Search term to filter posts",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Post"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->has('term')) {
            $term = $request->get('term');
            $query->where(function($q) use ($term) {
                $q->where('title', 'LIKE', "%{$term}%")
                  ->orWhere('content', 'LIKE', "%{$term}%")
                  ->orWhere('category', 'LIKE', "%{$term}%");
            });
        }

        return response()->json($query->paginate(10));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/posts",
     *     summary="Create a new post",
     *     description="Create a new blog post with the provided data",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PostRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(PostRequest $request)
    {
        $post = Post::create($request->validated());
        return response()->json($post, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/posts/{id}",
     *     summary="Get a specific post",
     *     description="Retrieve a specific blog post by its ID",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function show($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($post);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/posts/{id}",
     *     summary="Update a post",
     *     description="Update an existing blog post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PostRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(PostRequest $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        }
        $post->update($request->validated());
        return response()->json($post);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/posts/{id}",
     *     summary="Delete a post",
     *     description="Delete a specific blog post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Post deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        }
        $post->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
