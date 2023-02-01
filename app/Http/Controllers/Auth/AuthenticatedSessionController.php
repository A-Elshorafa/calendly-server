<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        // regenerate the session ID new session ID for logged in users
        // avoid expoliting of session fixation
        $request->session()->regenerate();

        return response(["success" => true, "data" => Auth::user()]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        // delete session data
        // avoid expoliting of session fixation
        $request->session()->invalidate();

        // regenerate sessionID just for tracking logged out users
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
