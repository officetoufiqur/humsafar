<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\LookingFor;
use App\Models\Profile;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    use ApiResponse;

    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $file = null;
        if ($request->hasFile('photo')) {
            $file = FileUpload::storeFile($request->file('photo'), 'uploads/users');
        }

        // Create user
        $user = User::create([
            'display_name' => $validated['display_name'],
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'dob' => $validated['dob'],
            'age' => $validated['age'],
            'photo' => $file,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_accept' => $validated['is_accept'],
            'is_permission' => $validated['is_permission'],
        ]);

        // Create profile
        $profile = Profile::create([
            'user_id' => $user->id,
            'gender' => $validated['gender'] ?? null,
            'origin' => $validated['origin'] ?? null,
            'looking_for' => $validated['looking_for'] ?? null,
            'relationship' => $validated['relationship'] ?? null,
            'children' => $validated['children'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'location' => $validated['location'] ?? null,
            'hair_color' => $validated['hair_color'] ?? null,
            'eye_color' => $validated['eye_color'] ?? null,
            'body_type' => $validated['body_type'] ?? null,
            'appearance' => $validated['appearance'] ?? null,
            'intelligence' => $validated['intelligence'] ?? null,
            'clothing' => $validated['clothing'] ?? null,
            'mother_tongue' => $validated['mother_tongue'] ?? null,
            'known_language' => $validated['known_language'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'education' => $validated['education'] ?? null,
            'career' => $validated['career'] ?? null,
            'about_me' => $validated['about_me'] ?? null,
            'sports' => $validated['sports'] ?? null,
            'music' => $validated['music'] ?? null,
            'cooking' => $validated['cooking'] ?? null,
            'reading' => $validated['reading'] ?? null,
            'tv_shows' => $validated['tv_shows'] ?? null,
            'personal_attitude' => $validated['personal_attitude'] ?? null,
            'smoke' => $validated['smoke'] ?? null,
            'drinking' => $validated['drinking'] ?? null,
            'going_out' => $validated['going_out'] ?? null,
            'membership_name' => $validated['membership_name'] ?? null,
            'membership_amount' => $validated['membership_amount'] ?? null,
        ]);

        // Create LookingFor
        $lookingFor = LookingFor::create([
            'user_id' => $user->id,
            'gender' => $validated['gender'] ?? null,
            'origin' => $validated['origin'] ?? null,
            'relationship' => $validated['relationship'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'age_range' => $validated['age_range'] ?? null,
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'education' => $validated['education'] ?? null,
            'children' => $validated['children'] ?? null,
            'smoke' => $validated['smoke'] ?? null,
            'drinking' => $validated['drinking'] ?? null,
            'going_out' => $validated['going_out'] ?? null,
            'location' => $validated['location'] ?? null,
            'distance_km' => $validated['distance_km'] ?? null,
        ]);

        $user = array_merge(
            $user->toArray(),
            $profile->toArray(),
            $lookingFor->toArray()
        );

        return $this->successResponse(
            $user,
            'Registration successful',
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = Str::lower($request->email).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many login attempts. Try again later.',
            ], 429);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::hit($key, 60);

            return $this->errorResponse('Invalid email or password', 401);
        }

        RateLimiter::clear($key);

        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'token' => $token,
            'user' => $user
        ];

        return $this->successResponse(
            $data,
            'Login successful',
        );
    }
}
