<?php

namespace App\Http\Controllers\Auth;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $admin = Admin::where('email', $request->email)->first();
        $hashedPassword = $admin?->password ?? Hash::make('dummy-password-for-timing-attack-prevention');

        if (! $admin || ! Hash::check($request->password, $hashedPassword)) {
            abort(HttpStatusCode::UNAUTHORIZED->value, 'Invalid credentials');
        }
        $token = $admin->createToken('admin-token')->plainTextToken;
        return new ApiResponseResource(
            ['token' => $token, 'type' => 'Bearer'],
            'Admin logged in successfully',
            HttpStatusCode::OK
        );
    }
}
