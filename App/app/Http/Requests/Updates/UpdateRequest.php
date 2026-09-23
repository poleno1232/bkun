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
                'prohibited_if:type,' . UpdateTypeEnum::SUBMISSION->value . ',' . UpdateTypeEnum::VOTE->value,
                'required_if:type,' . UpdateTypeEnum::TEXT->value,
            ],
            'title' => [
                'string',
                'prohibited_if:type,' . UpdateTypeEnum::TEXT->value,
                'required_if:type,' . UpdateTypeEnum::SUBMISSION->value . ',' . UpdateTypeEnum::VOTE->value,
            ],
            'votes' => [
                'array',
                'prohibited_if:type,' . UpdateTypeEnum::TEXT->value . ',' . UpdateTypeEnum::SUBMISSION->value,
            ],
            'closes_at' => [
                'nullable',
                'date_format:Y-m-d H:i:s',
                'after:' . now()->format('Y-m-d H:i:s'),
                'prohibited_if:type,' . UpdateTypeEnum::TEXT->value,
            ],
            'votes.*.id' => [
                'integer',
                'nullable',
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
