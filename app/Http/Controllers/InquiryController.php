<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Http\Requests\ContactRequest;
use App\Mail\InquiryReceived;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\Package;
use App\Services\WhatsAppMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Booking and contact forms: validate, keep the lead in the database, tell the team,
 * then send the visitor on to WhatsApp with the message prefilled.
 */
class InquiryController extends Controller
{
    public function __construct(private WhatsAppMessage $whatsapp) {}

    public function booking(BookingRequest $request): JsonResponse|RedirectResponse
    {
        if ($this->isBot($request)) {
            return $this->done($request, whatsapp_url());
        }

        $data = $request->validated();
        $package = filled($data['package'] ?? null) ? Package::published()->where('name->en', $data['package'])->first() : null;

        $inquiry = Inquiry::create([
            'type' => Inquiry::TYPE_BOOKING,
            'name' => trim($data['name']),
            'phone' => $data['phone'],
            'destination_id' => Destination::published()->where('name->en', $data['destination'])->value('id'),
            'package_id' => $package?->id,
            'travel_date' => $data['date'],
            'guests' => (int) $data['guests'],
            'estimated_total' => $package ? $package->price * (int) $data['guests'] : null,
            'message' => filled($data['message'] ?? null) ? trim($data['message']) : null,
        ] + $this->context($request));

        $this->notify($inquiry);

        return $this->done($request, $this->whatsapp->url($this->whatsapp->booking($inquiry->load(['destination', 'package']))));
    }

    public function contact(ContactRequest $request): JsonResponse|RedirectResponse
    {
        if ($this->isBot($request)) {
            return $this->done($request, whatsapp_url());
        }

        $data = $request->validated();

        $inquiry = Inquiry::create([
            'type' => Inquiry::TYPE_CONTACT,
            'name' => trim($data['name']),
            'phone' => $data['phone'],
            'email' => filled($data['email'] ?? null) ? trim($data['email']) : null,
            'topic' => $data['topic'],
            'message' => trim($data['message']),
        ] + $this->context($request));

        $this->notify($inquiry);

        return $this->done($request, $this->whatsapp->url($this->whatsapp->contact($inquiry)));
    }

    /** A hidden "website" field that people never see: if it is filled, a script did it. */
    private function isBot(Request $request): bool
    {
        return $request->filled('website');
    }

    /** @return array<string, mixed> */
    private function context(Request $request): array
    {
        return [
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
        ];
    }

    /** A mail failure must never stop the visitor from reaching WhatsApp. */
    private function notify(Inquiry $inquiry): void
    {
        try {
            Mail::to(site('notify_email') ?: site('email'))->send(new InquiryReceived($inquiry->loadMissing(['destination', 'package'])));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function done(Request $request, string $whatsappUrl): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['whatsapp_url' => $whatsappUrl]);
        }

        return redirect()->away($whatsappUrl);
    }
}
