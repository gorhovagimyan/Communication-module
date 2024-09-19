<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function __construct(protected PostService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('comments', 'attachments', 'reactions')->paginate(10);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = $this->service->createPost($request->validated());

        if ($request->hasFile('attachments')) {
            $this->service->createPostAttachments($request->file('attachments'), $post);
        }

        return new PostResource($post->load('attachments', 'user', 'reactions', 'comments'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::with('comments', 'attachments', 'reactions')->find($id);

        if ($post === null) {
            return response()->json(['message' => 'Post not found with id: '. $id], Response::HTTP_NOT_FOUND);
        }

        return new PostResource($post->load('attachments', 'user', 'reactions', 'comments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, string $id)
    {
        $post = Post::find($id);

        if ($post === null) {
            return response()->json(['message' => 'Post not found with id: '. $id], Response::HTTP_NOT_FOUND);
        }

        if ($post->user_id !== auth()->id()) {
            return response()->json(['error' => 'You do not have permission to access this resource.'], Response::HTTP_FORBIDDEN);
        }

        $updatedPost = $this->service->updatePost($post, $request->validated());

        return new PostResource($updatedPost->load('attachments', 'user', 'reactions', 'comments'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        if ($post === null) {
            return response()->json(['message' => 'Post not found with id: '. $id], Response::HTTP_NOT_FOUND);
        }

        if ($post->user_id !== auth()->id()) {
            return response()->json(['error' => 'You do not have permission to access this resource.'], Response::HTTP_FORBIDDEN);
        }

        $post->delete();

        return response()->json(['message' => 'Post has been deleted successfully.'], Response::HTTP_OK);
    }
}
