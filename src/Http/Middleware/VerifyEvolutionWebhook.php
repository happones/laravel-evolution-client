<?php

namespace Happones\LaravelEvolutionClient\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates and sanity-checks inbound Evolution API webhooks.
 *
 * Evolution API v2 does not sign webhook requests, but it forwards the custom
 * headers configured on the webhook (Instance::setWebhook(headers: [...])). When
 * config('evolution.webhook.secret') is set, the request must carry that value in
 * the config('evolution.webhook.secret_header') header, or it is rejected with 401.
 * Payloads without an "event" name are rejected with 422.
 */
class VerifyEvolutionWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('evolution.webhook.secret', '');

        if ($secret !== '') {
            $header = (string) config('evolution.webhook.secret_header', 'X-Evolution-Secret');
            $provided = (string) $request->header($header, '');

            if (! hash_equals($secret, $provided)) {
                return new JsonResponse(['status' => 'unauthorized'], 401);
            }
        }

        $event = $request->input('event', $request->input('type'));

        if (! is_string($event) || $event === '') {
            return new JsonResponse(['status' => 'invalid', 'message' => 'Missing webhook event name'], 422);
        }

        return $next($request);
    }
}
