<?php

namespace App\Exceptions;

use App\Jobs\DevMailNotification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
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
            // Create Notification Data
            $exception      = [
                "name"      => get_class($e),
                "message"   => $e->getMessage(),
                "file"      => $e->getFile(),
                "line"      => $e->getLine(),
            ];

            // Create a Job for Notification which will run after 5 seconds.
            $job = (new DevMailNotification($exception))->delay(5);

            // Dispatch Job and continue
            dispatch($job);
        });

        $this->renderable(function (\Exception $e, $request) {
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                //Might want to add emailing in here in case someone trying to brute force into system to keep track of IP's etc..
                return sendError('Unauthorized.', Response::HTTP_UNAUTHORIZED);
            }
            else {
                return sendError('An error has occurred.', Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
            }
        });
    }
}
