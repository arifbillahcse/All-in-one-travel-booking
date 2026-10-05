<?php

namespace App\Http\Requests\Concerns;

trait NormalizesPhone
{
    /** "+880 1711-000000" -> "+8801711000000" before validation. */
    protected function normalizePhone(): void
    {
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/[\s\-()]+/', '', (string) $this->input('phone'))]);
        }
    }

    protected function phoneRules(): array
    {
        return ['required', 'regex:/^\+?\d{10,15}$/'];
    }

    protected function phoneMessages(): array
    {
        return [
            'phone.required' => __('Please enter your phone number.'),
            'phone.regex' => __('Enter a valid number, e.g. +8801XXXXXXXXX.'),
        ];
    }
}
