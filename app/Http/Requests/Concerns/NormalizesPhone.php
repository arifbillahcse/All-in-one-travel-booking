<?php

namespace App\Http\Requests\Concerns;

trait NormalizesPhone
{
    /**
     * Before validation: "+880 1711-000000" -> "+8801711000000", and single-line fields lose line breaks
     * (a name goes into email subjects and WhatsApp text, so it must never contain a new line).
     */
    protected function normalizePhone(): void
    {
        foreach (['name', 'email', 'topic'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim(preg_replace('/\s+/u', ' ', $this->input($field)))]);
            }
        }

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
