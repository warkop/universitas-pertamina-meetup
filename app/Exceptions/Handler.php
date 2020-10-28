<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json([
                'status' => [
                    'system_message' => $exception->getMessage(),
                    'message' => 'Data tidak ditemukan atau sebagian data tidak valid!',
                    'code' => 404,
                ]
            ], 404);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'status' => [
                    'message' => 'Anda tidak diizinkan mengakses url ini!',
                    'code' => 403,
                ]
            ], 403);
        }

        if ($exception instanceof FileNotFoundException) {
            return response()->json([
                'status' => [
                    'message' => 'File tidak ada di server!',
                    'code' => 404,
                ]
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
