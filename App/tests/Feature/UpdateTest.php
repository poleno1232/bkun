<?php

namespace Tests\Feature;


use App\Contracts\Contentable;
use App\Enums\QuestStatusEnum;
use App\Enums\UpdateTypeEnum;
use App\Models\Chapter;
use App\Models\SubmissionUpdate;
use App\Models\TextUpdate;
use App\Models\VoteUpdate;
use Illuminate\Support\Uri;

class UpdateTest extends TestCase
{
    protected const array UPDATE_RESPONSE = [
        'id',
        'type',
        'title',
        'is_closed',
        'closes_at',
        'content',
    ];

    protected const array UPDATE_REQUESTS = [
        TextUpdate::class => [
            'type' => UpdateTypeEnum::TEXT->value,
            'text' => "Some giant text with narrative",
        ],
        VoteUpdate::class => [
            'type' => UpdateTypeEnum::VOTE->value,
            'title' => "Test title",
            'closes_at' => "2100-12-20 10:00:00",
            'votes' => [
                [
                    'value' => "Vote 1",
                ],
                [
                    'value' => "Vote 2",
                ],
            ],
        ],
        SubmissionUpdate::class => [
            'type' => UpdateTypeEnum::SUBMISSION->value,
            'title' => "Test title",
        ]
    ];

    /**
     * A basic feature test example.
     */
    public function testShowUpdate(): void
    {
        $this->authenticateUser();

        foreach ([
                    TextUpdate::class => UpdateTypeEnum::TEXT,
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            /** @var TextUpdate|VoteUpdate|SubmissionUpdate $update */
            $update = $class::firstOrFail();

            $response = $this->request(
                Uri::route('updates.show', [
                    'updateId' => $update->id,
                    'chapter' => $update->chapter_id,
                    'type' => $type->value,
                ]),
                'get',
            );

            $response->assertStatus(200);
            $response->assertJsonStructure(['data' => self::UPDATE_RESPONSE]);
        }
    }

    public function testShowUpdateNotAllowed(): void
    {
        $this->authenticateAnotherUser();

        foreach ([
                     TextUpdate::class => UpdateTypeEnum::TEXT,
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            /** @var TextUpdate|VoteUpdate|SubmissionUpdate $update */
            $update = $class::firstOrFail();
            $update->chapter->quest()->update(['status' => QuestStatusEnum::UNLISTED->value]);

            $response = $this->request(
                Uri::route('updates.show', [
                    'updateId' => $update->id,
                    'chapter' => $update->chapter_id,
                    'type' => $type->value,
                ]),
                'get',
            );

            $response->assertStatus(403);
        }
    }

    public function testStoreUpdateSuccess()
    {
        $this->authenticateUser();

        foreach ([
                     TextUpdate::class => UpdateTypeEnum::TEXT,
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            $chapter = Chapter::firstOrFail();
            $response = $this->request(
                Uri::route('updates.store', [
                    'chapter' => $chapter->id,
                    'type' => $type->value,
                ]),
                'post',
                data: self::UPDATE_REQUESTS[$class],
            );

            $response->assertStatus(201);
            $response->assertJsonStructure(['data' => self::UPDATE_RESPONSE]);
        }
    }

    public function testStoreUpdateFail(): void
    {
        $this->authenticateUser();

        foreach ([
                     TextUpdate::class => UpdateTypeEnum::TEXT,
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            $chapter = Chapter::firstOrFail();
            $response = $this->request(
                Uri::route('updates.store', [
                    'chapter' => $chapter->id,
                    'type' => $type->value,
                ]),
                'post',
                data: ['type' => $type->value],
            );

            $response->assertStatus(422);
        }
    }

    public function testStoreUpdateUnauthorized(): void
    {
        foreach ([
                     TextUpdate::class => UpdateTypeEnum::TEXT,
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            $chapter = Chapter::firstOrFail();
            $response = $this->request(
                Uri::route('updates.store', [
                    'chapter' => $chapter->id,
                    'type' => $type->value,
                ]),
                'post',
                data: self::UPDATE_REQUESTS[$class],
            );

            $response->assertStatus(401);
        }
    }

    public function testUpdateUpdateSuccess(): void
    {
        $this->authenticateUser();

        /** @var Chapter $chapter */
        $chapter = Chapter::whereHas('submissionUpdates')->firstOrFail();
        $updates = [
            TextUpdate::class => $chapter->textUpdates()->first(),
            SubmissionUpdate::class => $chapter->submissionUpdates()->first(),
            VoteUpdate::class => $chapter->votesUpdates()->first(),
        ];

        $vote = $updates[VoteUpdate::class]->votes()->create(['value' => 'Test']);

        foreach ([
                     TextUpdate::class => UpdateTypeEnum::TEXT,
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            $data = self::UPDATE_REQUESTS[$class];

            if ($class == VoteUpdate::class) {
                $data['votes'][] = ['id' => $vote->id, 'value' => "edited"];
            }

            $response = $this->request(
                Uri::route('updates.update', [
                    'chapter' => $chapter->id,
                    'type' => $type->value,
                    'updateId' => $updates[$class]->id,
                ]),
                'put',
                data: $data,
            );

            $response->assertStatus(200);
            $response->assertJsonStructure(['data' => self::UPDATE_RESPONSE]);
        }
    }

    public function testUpdateUpdateFail(): void
    {
        $this->authenticateUser();

        /** @var Chapter $chapter */
        $chapter = Chapter::whereHas('submissionUpdates')->firstOrFail();
        $updates = [
            TextUpdate::class => $chapter->textUpdates()->first(),
            SubmissionUpdate::class => $chapter->submissionUpdates()->first(),
            VoteUpdate::class => $chapter->votesUpdates()->first(),
        ];

        foreach ([
                     TextUpdate::class => UpdateTypeEnum::TEXT,
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            $response = $this->request(
                Uri::route('updates.update', [
                    'chapter' => $chapter->id,
                    'type' => $type->value,
                    'updateId' => $updates[$class]->id,
                ]),
                'put',
                data: ['type' => $type->value],
            );

            $response->assertStatus(422);
        }
    }

    public function testUpdateUpdateUnauthorized(): void
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::whereHas('submissionUpdates')->firstOrFail();
        $updates = [
            TextUpdate::class => $chapter->textUpdates()->first(),
            SubmissionUpdate::class => $chapter->submissionUpdates()->first(),
            VoteUpdate::class => $chapter->votesUpdates()->first(),
        ];

        foreach ([
                     TextUpdate::class => UpdateTypeEnum::TEXT,
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            $response = $this->request(
                Uri::route('updates.update', [
                    'chapter' => $chapter->id,
                    'type' => $type->value,
                    'updateId' => $updates[$class]->id,
                ]),
                'put',
                data: self::UPDATE_REQUESTS[$class],
            );

            $response->assertStatus(401);
        }
    }

    public function testOpenUpdateSuccess(): void
    {
        $this->authenticateUser();

        foreach ([
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            /** @var TextUpdate|VoteUpdate|SubmissionUpdate $update */
            $update = $class::firstOrFail();

            $response = $this->request(
                Uri::route('updates.open', [
                    'updateId' => $update->id,
                    'chapter' => $update->chapter_id,
                    'type' => $type->value,
                ]),
                'post',
            );

            $response->assertStatus(200);
            $response->assertJsonStructure(['data' => self::UPDATE_RESPONSE]);
        }
    }

    public function testOpenUpdateFail(): void
    {
        $this->authenticateUser();

        foreach ([
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            /** @var TextUpdate|VoteUpdate|SubmissionUpdate $update */
            $update = $class::firstOrFail();

            $response = $this->request(
                Uri::route('updates.open', [
                    'updateId' => $update->id,
                    'chapter' => $update->chapter_id,
                    'type' => 'test',
                ]),
                'post',
            );

            $response->assertStatus(422);
        }
    }

    public function testOpenUpdateNotFound(): void
    {
        $this->authenticateUser();

        foreach ([
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            /** @var TextUpdate|VoteUpdate|SubmissionUpdate $update */
            $update = $class::firstOrFail();

            $response = $this->request(
                Uri::route('updates.open', [
                    'updateId' => 0,
                    'chapter' => $update->chapter_id,
                    'type' => $type->value,
                ]),
                'post',
            );

            $response->assertStatus(404);
        }
    }

    public function testCloseUpdateSuccess(): void
    {
        $this->authenticateUser();

        foreach ([
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            /** @var TextUpdate|VoteUpdate|SubmissionUpdate $update */
            $update = $class::firstOrFail();

            $response = $this->request(
                Uri::route('updates.close', [
                    'updateId' => $update->id,
                    'chapter' => $update->chapter_id,
                    'type' => $type->value,
                ]),
                'post',
            );

            $response->assertStatus(200);
            $response->assertJsonStructure(['data' => self::UPDATE_RESPONSE]);
        }
    }

    public function testCloseUpdateFail(): void
    {
        $this->authenticateUser();

        foreach ([
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            /** @var TextUpdate|VoteUpdate|SubmissionUpdate $update */
            $update = $class::firstOrFail();

            $response = $this->request(
                Uri::route('updates.close', [
                    'updateId' => $update->id,
                    'chapter' => $update->chapter_id,
                    'type' => 'test',
                ]),
                'post',
            );

            $response->assertStatus(422);
        }
    }

    public function testCloseUpdateNotFound(): void
    {
        $this->authenticateUser();

        foreach ([
                     VoteUpdate::class => UpdateTypeEnum::VOTE,
                     SubmissionUpdate::class => UpdateTypeEnum::SUBMISSION,
                 ] as $class => $type) {
            /** @var TextUpdate|VoteUpdate|SubmissionUpdate $update */
            $update = $class::firstOrFail();

            $response = $this->request(
                Uri::route('updates.close', [
                    'updateId' => 0,
                    'chapter' => $update->chapter_id,
                    'type' => $type->value,
                ]),
                'post',
            );

            $response->assertStatus(404);
        }
    }
}
