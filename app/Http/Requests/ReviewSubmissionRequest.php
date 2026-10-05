<?php

namespace App\Http\Requests;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** A traveler's review. It is stored unapproved until someone moderates it. */
class ReviewSubmissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'destination' => ['required', Rule::in(
                Destination::published()->get()->map(fn (Destination $d) => $d->getTranslation('name', 'en'))->all()
            )],
            'rating' => ['required', 'integer', 'between:1,5'],
            'text' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('Please enter your name.'),
            'name.min' => __('Please enter your name.'),
            'destination.required' => __('Please choose a destination.'),
            'destination.in' => __('Please choose a destination.'),
            'rating.required' => __('Please choose a star rating.'),
            'rating.integer' => __('Please choose a star rating.'),
            'rating.between' => __('Please choose a star rating.'),
            'text.required' => __('Please write at least 20 characters.'),
            'text.min' => __('Please write at least 20 characters.'),
        ];
    }
}
