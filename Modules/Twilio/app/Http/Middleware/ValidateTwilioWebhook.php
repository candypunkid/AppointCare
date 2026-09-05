<?php

namespace Modules\Twilio\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Security\RequestValidator;

class ValidateTwilioWebhook
{
    public function handle(Request $request, Closure $next)
    {
        $validator = new RequestValidator(config('services.twilio.auth_token'));

        $url = $request->fullUrl();
        $params = $request->all();
        $signature = $request->header('X-Twilio-Signature', '');

        if (! $validator->validate($signature, $url, $params)) {
            Log::warning('Invalid Twilio webhook signature');

            return response('Unauthorized', 403);
        }

        return $next($request);
    }
}
