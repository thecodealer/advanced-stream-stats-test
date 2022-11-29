<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DefaultController extends Controller {
    public function index() {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('auth.index');
    }

    public function authIndex() {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth');
    }

    public function dashboard() {
        return view('dashboard');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}