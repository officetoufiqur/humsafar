<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => [
                'required',
                'min:6',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).+$/',
            ],
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'otp' => $otp,
        ]);

        // $user->sendEmailVerificationNotification();

        return $this->successResponse([
            'user' => $user,
            'otp' => $otp,
            'Sent Verify OTP to your phone number',
        ]);
    }

    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required',
        ]);

        $otp = User::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (! $otp) {
            return response()->json(['message' => 'Invalid OTP'], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        $user->update(['phone_verified_at' => now()]);

        $otp->delete();

        return response()->json(['message' => 'Phone verified']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return $this->errorResponse(
                'Invalid email or password',
                401
            );
        }

        $user = $request->user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
