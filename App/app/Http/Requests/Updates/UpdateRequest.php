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
                'prohibited_if:type,' . UpdateTypeEnum::SUBMISSION->value,
                'required_if:type,' . UpdateTypeEnum::TEXT->value
            ],
            'title' => [
                'string',
                'prohibited_if:type,' . UpdateTypeEnum::TEXT->value,
                'required_if:type,' . UpdateTypeEnum::SUBMISSION->value
            ]
        ];
    }
}
