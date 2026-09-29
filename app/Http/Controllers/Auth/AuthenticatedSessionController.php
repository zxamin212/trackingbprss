<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
        public function store(LoginRequest $request): RedirectResponse
        {
            $request->authenticate();

            $request->session()->regenerate();

            return redirect()->intended($this->redirectRoute());
        }

        private function redirectRoute(): string
        {
            return match (auth()->user()->role) {
                'admin' => route('admin.dashboard'),
                'cs' => route('cs.dashboard'),
                'slo' => route('slo.dashboard'),
                'admin_legal' => route('legal.dashboard'),
                'direksi' => route('direksi.dashboard'),
                'area_manager' => route('am.dashboard'),
                'manager_bisnis' => route('mb.dashboard'),
                default => route('login'),
            };
        }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}