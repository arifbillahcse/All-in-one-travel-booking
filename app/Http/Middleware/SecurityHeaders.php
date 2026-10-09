<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Browser security headers.
 *
 * The public site gets a strict Content-Security-Policy: scripts only from this site or with the
 * per-request nonce, no plugins, no framing. The admin panel (Filament/Livewire) needs inline and
 * eval'd scripts, so it only gets the headers that do not restrict scripts.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = $request->is('admin', 'admin/*', 'livewire', 'livewire/*', 'livewire-*');

        if (! $isAdmin) {
            $nonce = base64_encode(random_bytes(16));
            app()->instance('csp.nonce', $nonce);
            Vite::useCspNonce($nonce);
        }

        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
            'X-Frame-Options' => $isAdmin ? 'SAMEORIGIN' : 'DENY',
        ];

        if ($isAdmin || config('travelorio.noindex')) {
            $headers['X-Robots-Tag'] = 'noindex, nofollow';
        } else {
            $headers['Content-Security-Policy'] = $this->policy($nonce);
        }

        if ($request->isSecure() && app()->isProduction()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }

    private function policy(string $nonce): string
    {
        $url = parse_url((string) config('app.url'));
        $appOrigin = ($url['scheme'] ?? 'http').'://'.($url['host'] ?? 'localhost').(isset($url['port']) ? ':'.$url['port'] : '');

        $directives = [
            'default-src' => "'self'",
            'script-src' => "'self' 'nonce-{$nonce}'",
            'style-src' => "'self' https://fonts.googleapis.com",
            'style-src-attr' => "'unsafe-inline'",       // a few style="" attributes (rating bars, error pages)
            'font-src' => "'self' https://fonts.gstatic.com",
            'img-src' => "'self' data: https://picsum.photos {$appOrigin}",
            'connect-src' => "'self'",
            'frame-src' => 'https://www.openstreetmap.org',   // the map on the contact page
            'form-action' => "'self' https://wa.me https://api.whatsapp.com",
            'frame-ancestors' => "'none'",
            'base-uri' => "'self'",
            'object-src' => "'none'",
        ];

        if (app()->isProduction()) {
            $directives['upgrade-insecure-requests'] = '';
        }

        return collect($directives)->map(fn (string $value, string $name) => trim("{$name} {$value}"))->implode('; ');
    }
}
