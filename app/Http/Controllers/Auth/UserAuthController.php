<?php

namespace App\Http\Controllers\Auth;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        $hashedPassword = $user?->password ?? Hash::make('dummy-password-for-timing-attack-prevention');
        if (! $user || ! Hash::check($request->password, $hashedPassword)) {
            abort(HttpStatusCode::UNAUTHORIZED->value, 'Invalid credentials');
        }
        $token = $user->createToken('user-token')->plainTextToken;
        return new ApiResponseResource(
            ['token' => $token, 'type' => 'Bearer'],
            'User logged in successfully',
            HttpStatusCode::OK
        );
    }
}
