<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\CustomExceptionHandler;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    // public function report(Throwable $e)
    // {
    //     $ipAddress = request()->ip();
    
    //     if ($this->shouldReport($e)) {
    //         Log::error("[{$ipAddress}] " . $e->getMessage(), [
    //             'exception' => $e,
    //         ]);
    //     }
    
    //     parent::report($e);
    // }
     
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        
    }
}
