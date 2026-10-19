<?php

namespace App\Http\Requests\Quests;

use App\Enums\QuestStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestRequest extends QuestRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'status' => ['required', Rule::in(QuestStatusEnum::allowed())],
            'next_live_at' => ['nullable', 'date'],
            'verified_only' => ['required', 'boolean'],
        ]);
    }
}
