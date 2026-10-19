<?php

namespace App\Services;

use App\Enums\QuestStatusEnum;
use App\Models\Quest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuestService
{
    public function getQuery(array $params)
    {
        $quests = Quest::query()->with('user');

        if (isset($params['name'])) {
            $quests->where('name', 'like', '%' . $params['name'] . '%');
        }
        if (isset($params['user_id'])) {
            $quests->where('user_id', '=', $params['user_id']);
        }
        if (isset($params['status'])) {
            $quests->where('status', $params['status']);
        } else {
            $quests->orwhere(function ($query) use ($params) {
                $query->whereNotIn(
                    'status',
                    [QuestStatusEnum::HIDDEN->value, QuestStatusEnum::UNLISTED->value]
                )->orWhere('user_id', auth()->id());
            });
        }

        return $quests;
    }

    public function show(Quest $quest)
    {
        if ($quest->user_id == \auth()->id()) {
            return $quest;
        }

        return $quest;
    }

    public function create(array $data)
    {
        $quest = Quest::create(array_merge(
            $data, [
                'user_id' => auth()->id(),
                'status' => QuestStatusEnum::UNLISTED
            ]
        ));
        $quest->load(['user', 'chapters']);

        return $quest;
    }

    public function update(Quest $quest, array $data)
    {
        $quest->update($data);
        $quest->load(['user', 'chapters']);

        return $quest;
    }
}
