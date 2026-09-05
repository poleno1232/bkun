<?php

namespace App\Http\Requests;

use App\Enums\QuestStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchQuestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'status' => ['nullable', 'string', Rule::in(array_keys(QuestStatusEnum::labels()))],
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
