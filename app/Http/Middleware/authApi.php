<?php

namespace App\Http\Middleware;

use Closure;

class authApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(empty(auth()->user())){
            return response()->json([
                'status' => 'unsuccessful',
                'code' => '401',
                'message' => 'Không có quyền sử dụng',
                'payload' => []
            ]);
        }
        return $next($request);
    }
}
