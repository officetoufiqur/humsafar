<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Mail\SendOtp;
use App\Models\LookingFor;
use App\Models\Package;
use App\Models\Profile;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'location' => ['required', 'string'],
        ]);

        // Create user
        $user = User::create([
            'display_name' => $validated['display_name'],
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Create profile
        $profile = Profile::create([
            'user_id' => $user->id,
            'location' => $validated['location'] ?? null,
        ]);

        $lookingFor = LookingFor::create([
            'user_id' => $user->id,
        ]);

        // $otp = mt_rand(10000000, 99999999);
        $otp = '12345678';

        $user->otp = $otp;
        $user->save();

        Mail::to($user->email)->send(new SendOtp($otp));

        $token = $user->createToken('auth_token')->plainTextToken;

        $user = array_merge(
            $user->toArray(),
            $profile->toArray(),
            $lookingFor->toArray(),
            ['otp' => $otp],
            ['token' => $token],
        );

        return $this->successResponse(
            $user,
            'Registration successful',
        );
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'numeric'],
        ]);

        $user = User::where('email', $validated['email'])->where('otp', $validated['otp'])->first();

        if (! $user) {
            return $this->errorResponse('Invalid OTP', 401);
        }

        $user->otp = null;
        $user->email_verified_at = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            $user,
            'Registration successful',
        );
    }

    public function membership()
    {
        $package = Package::where('status', 1)->get();

        if (! $package) {
            return $this->errorResponse('Package not found', 404);
        }

        return $this->successResponse(
            $package,
            'Package fetched successfully',
        );
    }

    public function profile(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        // $file = null;
        // if ($request->hasFile('photo')) {
        //     $file = FileUpload::storeFile($request->file('photo'), 'uploads/users');
        // }

        $user = Auth::user();

        if (! $user) {
            return $this->errorResponse('User not found', 404);
        }

        $user->dob = $validated['dob'];
        $user->photo = $validated['photo'];
        $user->is_accept = $validated['is_accept'];
        $user->is_permission = $validated['is_permission'];
        $user->is_complete = 1;
        $user->save();

        // Create profile
        $profile = Profile::where('user_id', $user->id)->first();
        $profile->update([
            'gender' => $validated['gender'],
            'origin' => $validated['origin'],
            'age' => $validated['age'],
            'looking_for' => $validated['looking_for'],
            'relationship' => $validated['relationship'],
            'children' => $validated['children'],
            'religion' => $validated['religion'],
            'hair_color' => $validated['hair_color'],
            'eye_color' => $validated['eye_color'],
            'body_type' => $validated['body_type'],
            'appearance' => $validated['appearance'],
            'intelligence' => $validated['intelligence'],
            'clothing' => $validated['clothing'],
            'mother_tongue' => $validated['mother_tongue'],
            'known_language' => $validated['known_language'],
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'education' => $validated['education'],
            'career' => $validated['career'],
            'about_me' => $validated['about_me'],
            'sports' => $validated['sports'],
            'music' => $validated['music'],
            'cooking' => $validated['cooking'],
            'reading' => $validated['reading'],
            'tv_shows' => $validated['tv_shows'],
            'personal_attitude' => $validated['personal_attitude'],
            'smoke' => $validated['smoke'],
            'drinking' => $validated['drinking'],
            'going_out' => $validated['going_out'],
        ]);

        // Create LookingFor
        $lookingFor = LookingFor::where('user_id', $user->id)->first();
        $lookingFor->update([
            'looking_gender' => $validated['looking_gender'],
            'looking_origin' => $validated['looking_origin'],
            'looking_relationship' => $validated['looking_relationship'],
            'looking_religion' => $validated['looking_religion'],
            'looking_age_range' => $validated['looking_age_range'],
            'looking_height' => $validated['looking_height'],
            'looking_weight' => $validated['looking_weight'],
            'looking_education' => $validated['looking_education'],
            'looking_children' => $validated['looking_children'],
            'looking_smoke' => $validated['looking_smoke'],
            'looking_drinking' => $validated['looking_drinking'],
            'looking_going_out' => $validated['looking_going_out'],
            'looking_location' => $validated['looking_location'],
            'looking_distance_km' => $validated['looking_distance_km'],
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
            'user' => $user,
        ];

        return $this->successResponse(
            $data,
            'Login successful',
        );
    }

    public function fileUpload(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'file.*' => 'file|max:2048',
        ]);

        $uploadedFiles = [];

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $singleFile) {
                $uploadedFiles[] = FileUpload::storeFile($singleFile, 'uploads/users');
            }
        }

        return $this->successResponse(
            $uploadedFiles,
            'Files uploaded successfully'
        );
    }
}
