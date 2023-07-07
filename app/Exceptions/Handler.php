<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        return $request->wantsJson()
            ? mainResponse(false, $e->getMessage(), [], [], $e->getCode())
            : parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $guard = Arr::get($exception->guards(), 0);
        switch ($guard) {
            case 'admin':
                $login = '/admin/login';
                return $request->wantsJson()
                    ? mainResponse(false, __('unauthorized'), [], [], 401)
                    : redirect(url(locale() . $login));
            case 'sanctum':
                return mainResponse(false, __('unauthorized'), [], [], 401);
            default:
                $login = '/login';
                break;
        }
        return $request->wantsJson()
            ? mainResponse(false, __('unauthorized'), [], [], 401)
            : redirect()->guest(url(locale() . $login));
    }
}
