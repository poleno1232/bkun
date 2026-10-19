<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chats\ChatMessageRequest;
use App\Http\Requests\Chats\UpdateChatMessageRequest;
use App\Http\Resources\Chats\ChatMessageResource;
use App\Models\ChatMessage;
use App\Models\Quest;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    public function __construct(protected ChatService $chatService)
    {
        $this->ensureUserIsAuthenticated();
    }
    public function index(Quest $quest)
    {
        Gate::authorize('view', $quest);

        return ChatMessageResource::collection(
            ChatMessage::where('quest_id', $quest->id)->with('user')->orderByDesc('posted_at')->paginate()
        );
    }

    public function show(Quest $quest, ChatMessage $message)
    {
        Gate::authorize('view', $quest);

        return ChatMessageResource::make($message);
    }

    public function store(ChatMessageRequest $request, Quest $quest)
    {
        $this->ensureUserIsAuthenticated();
        Gate::authorize('canHaveAnonymousCommenters', $quest);

        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $this->chatService->create($validated, $quest);

        return response()->noContent(201);
    }

    public function update(UpdateChatMessageRequest $request, Quest $quest, ChatMessage $message)
    {
        Gate::authorize('view', $quest);
        Gate::authorize('update', $message);
        $validated = $request->validated();
        $this->chatService->update($validated, $message, $request->ip());

        return response()->noContent();
    }
}
