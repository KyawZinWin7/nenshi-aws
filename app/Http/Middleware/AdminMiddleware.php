<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // auth() ထဲက employee ကိုခေါ်
        $user = auth()->user();

        // role မရှိတာ သို့မဟုတ် admin မဟုတ်တာဆိုရင်
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            return redirect()->route('nenshioperations')->with('error', 'アクセス権限がありません。ss');
        }


        return $next($request);
    }
}
