<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class AuthenticateController extends Controller
{
    public function create ()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
        {
            $credentials = $request->validate([
                'employee_code' => 'required',
                'password' => 'required',
            ]);

            // Auth attempt
            if (Auth::attempt(['employee_code' => $credentials['employee_code'], 'password' => $credentials['password']], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended();
            }

            return back()->withErrors([
                'employee_code' => '社員コードまたはパスワードが正しくありません。',
            ])->onlyInput('employee_code');
        }

    public function destory(Request $request)
        {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();


            return redirect()->route('home');
        }
}
