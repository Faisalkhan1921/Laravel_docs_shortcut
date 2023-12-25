<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogIpAddressMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
//     public function handle($request, Closure $next)
// {
//     $response = $next($request);

//     if (config('app.env') === 'local') {
//         $ipAddress = request()->ip();

       
//         if ($response->isServerError() && $response->exception) {
//             $message = $response->exception->getMessage();
//             Log::error("IP Address: $ipAddress - $message");

            
//             $response->exception = null;
//         }
//     }

//     return $response;
// }

    


     public function handle($request, Closure $next)
    {
        $response = $next($request);
    
        if (config('app.env') === 'local') {
            $ipAddress = request()->ip();
    
            if (auth()->check()) {
                $userEmail = auth()->user()->email;
            } else {
                $userEmail = 'Guest';
            }
    
            if ($response->isServerError() && $response->exception) {
                $message = $response->exception->getMessage();
                Log::error("User Email: $userEmail - IP Address: $ipAddress - $message");
                $response->exception = null;
            }
        }
    
        return $response;
    }
}
