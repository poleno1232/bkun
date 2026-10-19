<?php

namespace App\Http\Requests\Updates;

use App\Enums\UpdateTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'type' => ['required', Rule::in(UpdateTypeEnum::values())],
            'text' => [
                'string',
                'required_if:type,' . UpdateTypeEnum::TEXT->value,
                'nullable',
                'exclude_if:type,' . UpdateTypeEnum::SUBMISSION->value . ',' . UpdateTypeEnum::VOTE->value,
            ],
            'title' => [
                'string',
                'nullable',
                'exclude_if:type,' . UpdateTypeEnum::TEXT->value,
                'required_if:type,' . UpdateTypeEnum::SUBMISSION->value . ',' . UpdateTypeEnum::VOTE->value,
            ],
            'votes' => [
                'array',
                'nullable',
                'exclude_if:type,' . UpdateTypeEnum::TEXT->value . ',' . UpdateTypeEnum::SUBMISSION->value,
            ],
            'closes_at' => [
                'sometimes',
                'exclude_if:type,' . UpdateTypeEnum::TEXT->value,
                'date_format:Y-m-d H:i:s',
                'after:' . now()->format('Y-m-d H:i:s'),
            ],
            'votes.*.id' => [
                'integer',
                'exists:votes,id',
            ],
            'votes.*.value' => [
                'string',
                'max:255',
                'min:3',
            ],
        ];
    }
}
