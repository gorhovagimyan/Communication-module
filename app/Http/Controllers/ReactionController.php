<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReactionRequest;
use App\Http\Resources\ReactionResource;
use App\Models\Reaction;
use App\Services\ReactionService;
use Symfony\Component\HttpFoundation\Response;

class ReactionController extends Controller
{
    public function __construct(protected ReactionService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reactions = Reaction::with('post', 'comment', 'user')->paginate(10);

        return ReactionResource::collection($reactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReactionRequest $request)
    {
        $reaction = $this->service->createReaction($request->validated());

        return new ReactionResource($reaction->load('post', 'comment', 'user'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reaction = Reaction::with('post', 'comment', 'user')->find($id);

        if ($reaction === null) {
            return response()->json(['message' => 'Reaction not found with id: '. $id], Response::HTTP_NOT_FOUND);
        }

        return new ReactionResource($reaction->load('post', 'comment', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreReactionRequest $request, string $id)
    {
        $reaction = Reaction::find($id);

        if ($reaction === null) {
            return response()->json(['message' => 'Reaction not found with id: '. $id], Response::HTTP_NOT_FOUND);
        }

        if ($reaction->user_id !== auth()->id()) {
            return response()->json(['error' => 'You do not have permission to access this resource.'], Response::HTTP_FORBIDDEN);
        }

        $updatedReaction = $this->service->updateReaction($reaction, $request->validated());

        return new ReactionResource($updatedReaction->load('post', 'comment', 'user'));    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reaction = Reaction::find($id);

        if ($reaction === null) {
            return response()->json(['message' => 'Reaction not found with id: '. $id], Response::HTTP_NOT_FOUND);
        }

        if ($reaction->user_id !== auth()->id()) {
            return response()->json(['error' => 'You do not have permission to access this resource.'], Response::HTTP_FORBIDDEN);
        }

        $reaction->delete();

        return response()->json(['message' => 'Post has been deleted successfully.'], Response::HTTP_OK);
    }
}
