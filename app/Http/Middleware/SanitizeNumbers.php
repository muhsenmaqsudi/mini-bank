<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeNumbers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->merge([
            'sender_card' => $request->has('sender_card') ? to_en_number($request->input('sender_card')) : '',
            'receiving_card' => $request->has('receiving_card') ? to_en_number($request->input('receiving_card')) : '',
            'amount' => $request->has('amount') ? to_en_number($request->input('amount')) : '',
        ]);

        return $next($request);
    }
}
