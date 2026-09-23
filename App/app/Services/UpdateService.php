<?php

namespace App\Services;

use App\Contracts\Contentable;
use App\Enums\UpdateTypeEnum;
use App\Models\Chapter;
use App\Models\SubmissionUpdate;
use App\Models\TextUpdate;
use App\Models\Vote;
use App\Models\VoteUpdate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Mews\Purifier\Facades\Purifier;

class UpdateService
{
    public function getUpdate(int $updateId, UpdateTypeEnum $type)
    {
        return match ($type) {
            UpdateTypeEnum::VOTE => VoteUpdate::findOrFail($updateId),
            UpdateTypeEnum::SUBMISSION => SubmissionUpdate::findOrFail($updateId),
            UpdateTypeEnum::TEXT => TextUpdate::findOrFail($updateId),
        };
    }

    public function create(array $data, Chapter $chapter): Contentable
    {
        return match ($data['type']) {
            UpdateTypeEnum::TEXT->value => $this->createTextUpdate($data, $chapter),
            UpdateTypeEnum::SUBMISSION->value => $this->createSubmissionUpdate($data, $chapter),
            UpdateTypeEnum::VOTE->value => $this->createVoteUpdate($data, $chapter),
            default => throw ValidationException::withMessages([
                'type' => "Unsupported update type",
            ]),
        };
    }

    public function edit(array $data, int $updateId): Contentable
    {
        return match ($data['type']) {
            UpdateTypeEnum::TEXT->value => $this->editTextUpdate($data, TextUpdate::findOrFail($updateId)),
            UpdateTypeEnum::SUBMISSION->value => $this->editSubmissionUpdate($data, SubmissionUpdate::findOrFail($updateId)),
            UpdateTypeEnum::VOTE->value => $this->editVoteUpdate($data, VoteUpdate::findOrFail($updateId)),
            default => throw ValidationException::withMessages([
                'type' => "Unsupported update type",
            ])
        };
    }

    protected function createTextUpdate(array $data, Chapter $chapter): Model|TextUpdate
    {
        return TextUpdate::create([
            'text' => Purifier::clean($data['text']),
            'chapter_id' => $chapter->id,
            'posted_at' => now(),
        ]);
    }

    protected function editTextUpdate(array $data, TextUpdate $update): Model|TextUpdate
    {
        $update->update([
            'text' => Purifier::clean($data['text']),
        ]);

        return $update;
    }

    protected function createSubmissionUpdate(array $data, Chapter $chapter): Model|SubmissionUpdate
    {
        return SubmissionUpdate::create([
            'chapter_id' => $chapter->id,
            'name' => $data['title'],
            'posted_at' => now(),
            'closes_at' => $data['closed_at'],
        ]);
    }

    protected function editSubmissionUpdate(array $data, SubmissionUpdate $update): Model|SubmissionUpdate
    {
        $update->update([
            'name' => $data['title'],
            'closes_at' => $data['closed_at'] ?? $update->closes_at,
        ]);

        return $update;
    }

    protected function createVoteUpdate(array $data, Chapter $chapter): Model|VoteUpdate
    {
        /** @var VoteUpdate $voteUpdate */
        $voteUpdate = VoteUpdate::create([
            'name' => $data['title'] ?? "Select vote",
            'posted_at' => now(),
            'closes_at' => $data['closes_at'] ?? null,
            'chapter_id' => $chapter->id,
        ]);

        if (isset($data['votes'])) {
            foreach ($data['votes'] as $vote) {
                $voteUpdate->votes()->create([
                    'value' => $vote['value'],
                    'vote_update_id' => $voteUpdate->id,
                ]);
            }
        }

        return $voteUpdate;
    }

    protected function editVoteUpdate(array $data, VoteUpdate $voteUpdate): Model|VoteUpdate
    {
        $voteUpdate->update([
            'name' => $data['title'] ?? $voteUpdate->name,
            'closes_at' => $data['closes_at'] ?? null,
        ]);

        if (isset($data['votes'])) {
            foreach ($data['votes'] as $vote) {
                if (isset($vote['id'])) {
                    Vote::where('id', $vote['id'])->update(['value' => $vote['value']]);
                    continue;
                }

                $voteUpdate->votes()->create([
                    'value' => $vote['value'],
                    'vote_update_id' => $voteUpdate->id,
                ]);
            }
        }
        $voteUpdate->load('votes');

        return $voteUpdate;
    }

    public function open(int $updateId): Contentable|Model
    {
        $update = VoteUpdate::find($updateId);
        if (!$update) {
            throw new ModelNotFoundException();
        }

        $update->update(['is_closed' => false]);
        return $update->refresh();
    }

    public function close(int $updateId): Contentable|Model
    {
        /** @var Contentable $update */
        $update = VoteUpdate::find($updateId);
        if (!$update) {
            throw new ModelNotFoundException();
        }

        $update->update(['is_closed' => true, 'closes_at' => $update->closes_at ?? now()]);
        return $update->refresh();
    }
}
