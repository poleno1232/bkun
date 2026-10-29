<?php

namespace App\Services;

use App\DTO\DiceDataDTO;
use App\Enums\UpdateTypeEnum;
use App\Http\Requests\Chats\UpdateChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\Quest;
use App\Models\Submission;
use App\Services\DiceRoller\DiceRollerService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ChatService
{
    private const array ROLL_COMMANDS = [
        '/dice',
        '/roll',
    ];
    public function __construct(
        private readonly DiceRollerService $diceRollerService,
        private readonly UpdateService $updateService,
    ) {}

    public function create(array $data, Quest $quest): Model|ChatMessage
    {
        $data['quest_id'] = $quest->id;

        /** @var ChatMessage $msg */
        $msg = ChatMessage::create($data);
        if (Str::startsWith($msg->message, self::ROLL_COMMANDS)) {
            $matched = collect(self::ROLL_COMMANDS)->first(function ($needle) use ($msg) {
                return str_starts_with($msg->message, $needle);
            });

            try {
                $diceData = $this->diceRollerService->parse($msg->message);
                $matched == '/dice' ? $this->parseDice($diceData, $msg) : $this->parseRoll($diceData, $msg);
            } catch (\Throwable) {
                //
            }
        }

        return ChatMessage::create($data);
    }

    public function update(array $data, ChatMessage $message, string $ip): bool
    {
        if ($message->ip != $ip) {
            throw ValidationException::withMessages([
                'ip' => "Message does not belong to IP address",
            ]);
        }

        return $message->update($data);
    }

    protected function parseDice(DiceDataDTO $diceData, ChatMessage $message): void
    {
        if ($message->user_id == $message->quest->user_id) {
            $message->load(['quest']);
            $this->updateService->create([
                'type' => UpdateTypeEnum::TEXT->value,
                'text' => $diceData->toString(),
            ], $message->quest->chapters()->orderByDesc('order')->first()->id);
        }
    }

    protected function parseRoll(DiceDataDTO $diceData, ChatMessage $message): void
    {
        $message->load(['quest']);
        $chapter = $message->quest->chapters()
            ->whereHas('submissionUpdates', function (Builder $builder) {
                $builder->where('is_closed', false)->orderByDesc('posted_at');
            })
            ->orderByDesc('order')
            ->first();

        if (!$chapter) {
            return;
        }

        Submission::create([
            'submission_update_id' => $chapter->submissionUpdates->first()->id,
            'user_id' => $message->user_id,
            'posted_at' => now(),
            'text' => $diceData->toString(),
        ]);
    }
}
