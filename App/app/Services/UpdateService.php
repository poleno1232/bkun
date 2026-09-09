<?php

namespace App\Services;

use App\Contracts\Contentable;
use App\Enums\UpdateTypeEnum;
use App\Models\Chapter;
use App\Models\SubmissionUpdate;
use App\Models\TextUpdate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Mews\Purifier\Facades\Purifier;

class UpdateService
{
    public function create(array $data, Chapter $chapter): Contentable
    {
        return match ($data['type']) {
            UpdateTypeEnum::TEXT->value => $this->createTextUpdate($data, $chapter),
            UpdateTypeEnum::SUBMISSION->value => $this->createSubmissionUpdate($data, $chapter),
            default => throw ValidationException::withMessages([
                'type' => "Unsupported update type",
            ]),
        };
    }

    public function edit(array $data, int $updateId): Contentable
    {
        return match ($data['type']) {
            UpdateTypeEnum::TEXT->value => $this->editTextUpdate($data, TextUpdate::firstOrFail($updateId)),
            UpdateTypeEnum::SUBMISSION->value => $this->editSubmissionUpdate($data, SubmissionUpdate::firstOrFail($updateId)),
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
        ]);
    }

    protected function editSubmissionUpdate(array $data, SubmissionUpdate $update): Model|SubmissionUpdate
    {
        $update->update([
            'name' => $data['title'],
        ]);

        return $update;
    }
}
