<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. चेक करो कि यूजर लॉगिन है या नहीं
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. अगर यूजर 'superadmin' है, तो उसे सब कुछ एक्सेस करने दो
        if ($user->role == 'superadmin') {
            return $next($request);
        }

        // 3. चेक करो कि यूजर का रोल अलाउड लिस्ट में है या नहीं
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. अगर एक्सेस नहीं है, तो 403 Forbidden एरर या डैशबोर्ड पर भेज दो
        abort(403, 'भाई, आपको इस पेज का एक्सेस नहीं है!');
    }
}
