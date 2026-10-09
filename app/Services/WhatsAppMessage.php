<?php

namespace App\Services;

use App\Models\Inquiry;

/**
 * Builds the prefilled WhatsApp text for a saved inquiry, in the visitor's language.
 * The wording reuses the keys in lang/bn.json, so Bangla and English stay in step.
 */
class WhatsAppMessage
{
    public function url(string $message): string
    {
        return whatsapp_url($message);
    }

    public function booking(Inquiry $inquiry): string
    {
        $lines = [
            t("Hello TravelOrio! I'd like to book a trip."),
            '',
            t('Name: {v}', ['v' => $inquiry->name]),
            t('Phone: {v}', ['v' => $inquiry->phone]),
            t('Destination: {v}', ['v' => $inquiry->destination?->name]),
            t('Package: {v}', ['v' => $inquiry->package?->name ?? __('Not sure yet')]),
            t('Travel date: {v}', ['v' => format_date($inquiry->travel_date)]),
            t('Travelers: {v}', ['v' => to_locale_digits($inquiry->guests)]),
        ];

        if ($label = $this->estimateLabel($inquiry)) {
            $lines[] = t('Estimated total: {v}', ['v' => $label]);
        }

        if (filled($inquiry->message)) {
            array_push($lines, '', t('Message: {v}', ['v' => trim($inquiry->message)]));
        }

        return implode("\n", $lines);
    }

    public function contact(Inquiry $inquiry): string
    {
        $lines = [
            t('Hello TravelOrio! I have a question.'),
            '',
            t('Name: {v}', ['v' => $inquiry->name]),
            t('Phone: {v}', ['v' => $inquiry->phone]),
        ];

        if (filled($inquiry->email)) {
            $lines[] = t('Email: {v}', ['v' => $inquiry->email]);
        }

        array_push($lines, t('Topic: {v}', ['v' => __($inquiry->topic)]), '', trim((string) $inquiry->message));

        return implode("\n", $lines);
    }

    /** @param array{name: string, destination: string, rating: int, text: string} $review */
    public function review(array $review): string
    {
        return implode("\n", [
            t('New review for TravelOrio (please moderate):'),
            '',
            t('Name: {v}', ['v' => $review['name']]),
            t('Destination: {v}', ['v' => __($review['destination'])]),
            t('Rating: {v} / 5', ['v' => to_locale_digits($review['rating'])]),
            '',
            trim($review['text']),
        ]);
    }

    /** "৳50,000 (4 × ৳12,500)", or null when no plan was chosen. */
    public function estimateLabel(Inquiry $inquiry): ?string
    {
        if (! $inquiry->package || ! $inquiry->guests) {
            return null;
        }

        return t('{total} ({n} × {price})', [
            'total' => format_money($inquiry->package->price * $inquiry->guests),
            'n' => to_locale_digits($inquiry->guests),
            'price' => format_money($inquiry->package->price),
        ]);
    }
}
