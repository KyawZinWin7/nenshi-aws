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
        if (!$user || $user->role !== 'admin') {
            // 👇 redirect ပြုလုပ်
            return redirect()->route('home')->with('error', 'アクセス権限がありません。');
        }

        return $next($request);
    }
}
