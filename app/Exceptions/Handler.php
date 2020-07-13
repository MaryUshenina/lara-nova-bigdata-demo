<?php

namespace App\Exceptions;

use App\Nova\Ad;
use App\Nova\Requests\PostSizeInterface;
use App\Nova\Requests\PostSizeTrait;
use App\Nova\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler implements PostSizeInterface
{
    use PostSizeTrait;

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
        if ($exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {

            $filedName = $this->getReferrerResourceField($request) ?? 'image';

            return response()->json([
                "message" => "The given data was invalid.",
                "errors" => [
                    $filedName => ["The $filedName may not be greater than ".self::getMaxPostSizeInKiloBytes()." kilobytes."],
                ]
            ], 422);
        }

        return parent::render($request, $exception);
    }

    private function getReferrerResourceField($request)
    {
        $keys = [
            User::uriKey() => 'avatar',
            Ad::uriKey() => 'photo',
        ];

        if ($referer = $request->headers->get('referer')) {
            foreach ($keys as $key => $imageField) {
                if (strpos($referer, $key) !== false) {
                    return $imageField;
                }
            }
        }

        return null;
    }
}
