<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    use NormalizesPhone;

    /** Option values of the topic select (English). */
    public const TOPICS = ['Planning a trip', 'Group or corporate tour', 'Existing booking', 'Partnership', 'Something else'];

    protected function prepareForValidation(): void
    {
        $this->normalizePhone();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => $this->phoneRules(),
            'email' => ['nullable', 'email:rfc', 'max:190'],
            'topic' => ['required', Rule::in(self::TOPICS)],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return $this->phoneMessages() + [
            'name.required' => __('Please enter your name.'),
            'name.min' => __('Name looks too short.'),
            'email.email' => __('Enter a valid email address.'),
            'topic.in' => __('Please choose a topic.'),
            'topic.required' => __('Please choose a topic.'),
            'message.required' => __('Please write at least 10 characters.'),
            'message.min' => __('Please write at least 10 characters.'),
        ];
    }
}
