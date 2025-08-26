<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;



class RegisterController extends Controller
{
    public function create(){
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|max:255',
            'email'=> 'required|lowercase|email|max:255',
            'password'=> 'required|confirmed|min:3'
        ]);
        $user = User::create($credentials);

        //send verification email

        Auth::login($user);

        return redirect()->route('home');

    }
}
