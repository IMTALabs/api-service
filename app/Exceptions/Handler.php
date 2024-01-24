<?php

namespace App\Exceptions;

// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Essa\APIToolKit\Exceptions\Handler as APIHandler;
use Illuminate\Http\Request as RequestAlias;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends APIHandler
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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  RequestAlias  $request
     * @param  Throwable     $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, $e): Response
    {
        return parent::render($request, $e);

        // if ($request->expectsJson()) {
        //     if ($e instanceof \BadMethodCallException) {
        //         return $this->responseWithCustomError(
        //             title: 'Bad Method Call Exception',
        //             details: $e->getMessage(),
        //             statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
        //         );
        //     }
        // }
    }
}
