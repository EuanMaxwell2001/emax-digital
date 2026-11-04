<?php

namespace App\Http\Controllers;

use App\Mail\ContactReceived;
use App\Models\ContactSubmission;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /** Forms sent faster than this after the page loaded are treated as bots. */
    private const MIN_SECONDS = 3;

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Bots: a filled honeypot or an instant submit gets a normal-looking reply, nothing saved.
        if (filled($request->input('website')) || $this->tooFast($request->input('started'))) {
            return $this->sent($request);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'name.required' => 'Please tell me your name.',
            'email.required' => 'I need an email address to reply to.',
            'email.email' => 'That email address does not look right.',
            'message.required' => 'Tell me a little about what you need.',
            'message.min' => 'Could you say a little more? A sentence or two is plenty.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect($this->returnUrl($request))->withErrors($validator)->withInput();
        }

        $submission = ContactSubmission::create($validator->validated() + [
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 490, ''),
            'page' => Str::limit($this->returnPath($request), 250, ''),
        ]);

        // Email after the response is sent, so the visitor never waits on SMTP.
        dispatch(function () use ($submission) {
            try {
                Mail::to(config('site.contact_to'))->send(new ContactReceived($submission));
            } catch (\Throwable $e) {
                report($e); // it is saved in the admin either way
            }
        })->afterResponse();

        return $this->sent($request);
    }

    private function sent(Request $request): JsonResponse|RedirectResponse
    {
        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : redirect($this->returnUrl($request))->with('contact_sent', true);
    }

    private function tooFast(?string $token): bool
    {
        try {
            $started = (int) decrypt((string) $token);
        } catch (DecryptException) {
            return true;
        }

        return now()->getTimestamp() - $started < self::MIN_SECONDS;
    }

    /** The page the form was on (a local path only), so no-JS visitors land back on it. */
    private function returnPath(Request $request): string
    {
        $page = (string) $request->input('page', '/');

        return Str::startsWith($page, '/') && ! Str::startsWith($page, ['//', '/\\']) ? $page : '/';
    }

    private function returnUrl(Request $request): string
    {
        return url($this->returnPath($request)).'#contact';
    }
}
