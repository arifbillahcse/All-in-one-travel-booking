<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use App\Models\Destination;
use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Booking request from the home page, the packages page and a destination page. */
class BookingRequest extends FormRequest
{
    use NormalizesPhone;

    protected function prepareForValidation(): void
    {
        $this->normalizePhone();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => $this->phoneRules(),
            'destination' => ['required', Rule::in($this->destinationNames())],
            'package' => ['nullable', Rule::in($this->packageNames())],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'guests' => ['required', 'integer', 'min:1', 'max:50'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return $this->phoneMessages() + [
            'name.required' => __('Please enter your name.'),
            'name.min' => __('Name looks too short.'),
            'destination.required' => __('Please choose a destination.'),
            'destination.in' => __('Please choose a destination.'),
            'package.in' => __('Please choose a destination.'),
            'date.required' => __('Please pick a travel date.'),
            'date.date' => __('Please pick a travel date.'),
            'date.after_or_equal' => __('Travel date can\'t be in the past.'),
            'guests.required' => __('Enter the number of travelers.'),
            'guests.integer' => __('Enter the number of travelers.'),
            'guests.min' => __('Choose between 1 and 50 travelers.'),
            'guests.max' => __('Choose between 1 and 50 travelers.'),
        ];
    }

    /** @return list<string> English names, which are the option values in the forms. */
    private function destinationNames(): array
    {
        return Destination::published()->get()->map(fn (Destination $d) => $d->getTranslation('name', 'en'))->all();
    }

    /** @return list<string> */
    private function packageNames(): array
    {
        return Package::published()->get()->map(fn (Package $p) => $p->getTranslation('name', 'en'))->all();
    }
}
