<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Sisipkan konteks ke SETIAP baris log error → mudah ditelusuri di log file:
        // siapa user-nya, route & URL mana, parameter apa (mis. id atlet).
        $this->context(fn () => $this->logContext());

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /** Konteks aman (hanya skalar/id) untuk dilampirkan ke log. */
    private function logContext(): array
    {
        if (app()->runningInConsole()) {
            return [];
        }

        $ctx = [];

        try {
            if ($user = auth()->user()) {
                $ctx['user_id'] = $user->id;
                $ctx['user'] = $user->email ?? null;
                $ctx['role'] = $user->role ?? null;
            }

            $request = request();
            $ctx['url'] = $request->fullUrl();
            $ctx['method'] = $request->method();
            $ctx['ip'] = $request->ip();

            if ($route = $request->route()) {
                $ctx['route'] = $route->getName();
                $params = [];
                foreach ($route->parameters() as $k => $v) {
                    // Jangan log seluruh model: cukup primary key-nya.
                    $params[$k] = is_object($v) && method_exists($v, 'getKey')
                        ? $v->getKey()
                        : (is_scalar($v) ? $v : gettype($v));
                }
                if ($params) {
                    $ctx['params'] = $params;
                }
            }
        } catch (Throwable) {
            // Konteks bersifat best-effort; jangan sampai menggagalkan pelaporan error.
        }

        return $ctx;
    }
}
