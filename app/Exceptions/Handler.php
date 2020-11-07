<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof OAuthServerException) {
            // grant type is not supported
            if ($e->getCode() === 2) {
                return response()->json(['error' => 'grant type is not supported', 'detail' => $e->getMessage(), 'code' => $e->getCode()], 400);
            }
            // client authentication failed
            if ($e->getCode() === 4) {
                return response()->json(['error' => 'Client authentication failed', 'detail' => $e->getMessage(), 'code' => $e->getCode()], 401);
            }
            // user authentication failed
            if ($e->getCode() === 10) {
                return response()->json(['error' => 'User authentication failed', 'detail' => $e->getMessage(), 'code' => $e->getCode()], 401);
            }
        }
        if ($e instanceof AuthenticationException) {
            return response()->json(['error' => 'unauthenticated', 'detail' => $e->getMessage(), 'code' => $e->getCode()], 401);
        }

        if ($e instanceof HttpException) {
            return response()->json(['error' => 'route error', 'detail' => $e->getMessage(), 'code' => $e->getCode()], 404);
        }

        if ($e instanceof QueryException) {
            return response()->json(['error' => 'query error', 'detail' => $e->errorInfo[2] . ' ' . $e->getSql(), 'code' => $e->getCode()], 400);
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->json(['error' => 'model error', 'detail' => $e->getMessage(), 'code' => $e->getCode()], 400);
        }

        if ($e instanceof ValidationException) {
            return response()->json(['error' => 'validation error', 'detail' => $e->validator->errors(), 'code' => $e->getCode()], 400);
        }
        return parent::render($request, $e);
    }
}
