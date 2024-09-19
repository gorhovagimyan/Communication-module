<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function __construct(protected CommentService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::with('post', 'user', 'attachments', 'reactions')->paginate(10);

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $comment = $this->service->createComment($request->validated());

        if ($request->hasFile('attachments')) {
            $this->service->createCommentAttachments($request->file('attachments'), $comment);
        }

        return new CommentResource($comment->load('post', 'user', 'attachments', 'reactions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comment = Comment::find($id);

        if ($comment === null) {
            return response()->json(['message' => 'Comment not found. Id: '. $id], Response::HTTP_NOT_FOUND);
        }

        return new CommentResource($comment->load('post', 'user', 'attachments', 'reactions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::find($id);

        if ($comment === null) {
            return response()->json(['message' => 'Comment not found. Id: '. $id], Response::HTTP_NOT_FOUND);
        }

        if ($comment->user_id !== auth()->id()) {
            return response()->json(['error' => 'You do not have permission to access this resource.'], Response::HTTP_FORBIDDEN);
        }

        $updatedComment = $this->service->updateComment($comment, $request->validated());

        return new CommentResource($updatedComment->load('post', 'user', 'attachments', 'reactions'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);

        if ($comment === null) {
            return response()->json(['message' => 'Comment not found. Id: '. $id], Response::HTTP_NOT_FOUND);
        }

        if ($comment->user_id !== auth()->id()) {
            return response()->json(['error' => 'You do not have permission to access this resource.'], Response::HTTP_FORBIDDEN);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment has been deleted successfully.'], Response::HTTP_OK);
    }
}
