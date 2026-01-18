<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\LookingFor;
use App\Models\Profile;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MyProfileController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $user = Auth::user();

        $profile = User::with('profile', 'lookingFor')->where('id', $user->id)->first();

        return $this->successResponse(
            $profile,
            'Profile fetched successfully',
        );
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $file = null;
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                FileUpload::deleteFile($user->photo);
            }
            $file = FileUpload::storeFile($request->file('photo'), 'uploads/users');
        }

        if (! $user) {
            return $this->errorResponse('User not found', 404);
        }

        $user->display_name = $request->display_name;
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->dob = $request->dob;
        $user->photo = $file;
        $user->save();

        // Create profile
        $profile = Profile::where('user_id', $user->id)->first();

        $profile->gender = $request->gender;
        $profile->origin = $request->origin;
        $profile->age = $request->age;
        $profile->looking_for = $request->looking_for;
        $profile->relationship = $request->relationship;
        $profile->children = $request->children;
        $profile->religion = $request->religion;
        $profile->location = $request->location;
        $profile->hair_color = $request->hair_color;
        $profile->eye_color = $request->eye_color;
        $profile->body_type = $request->body_type;
        $profile->appearance = $request->appearance;
        $profile->intelligence = $request->intelligence;
        $profile->clothing = $request->clothing;
        $profile->mother_tongue = $request->mother_tongue;
        $profile->known_language = $request->known_language;
        $profile->weight = $request->weight;
        $profile->height = $request->height;
        $profile->education = $request->education;
        $profile->career = $request->career;
        $profile->about_me = $request->about_me;
        $profile->sports = $request->sports;
        $profile->music = $request->music;
        $profile->cooking = $request->cooking;
        $profile->reading = $request->reading;
        $profile->tv_shows = $request->tv_shows;
        $profile->personal_attitude = $request->personal_attitude;
        $profile->smoke = $request->smoke;
        $profile->drinking = $request->drinking;
        $profile->going_out = $request->going_out;
        $profile->save();

        // Create LookingFor
        $lookingFor = LookingFor::where('user_id', $user->id)->first();

        $lookingFor->looking_gender = $request->looking_gender;
        $lookingFor->looking_origin = $request->looking_origin;
        $lookingFor->looking_relationship = $request->looking_relationship;
        $lookingFor->looking_religion = $request->looking_religion;
        $lookingFor->looking_age_range = $request->looking_age_range;
        $lookingFor->looking_height = $request->looking_height;
        $lookingFor->looking_weight = $request->looking_weight;
        $lookingFor->looking_education = $request->looking_education;
        $lookingFor->looking_children = $request->looking_children;
        $lookingFor->looking_smoke = $request->looking_smoke;
        $lookingFor->looking_drinking = $request->looking_drinking;
        $lookingFor->looking_going_out = $request->looking_going_out;
        $lookingFor->looking_location = $request->looking_location;
        $lookingFor->looking_distance_km = $request->looking_distance_km;
        $lookingFor->save();

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

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
            'confirm_password' => ['required', 'same:password'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['Current password is incorrect.'],
                ],
            ], 422);
        }

        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['New password must be different from old password.'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $user->tokens()->delete();

        return $this->successResponse(
            $user,
            'Password updated successfully',
        );
    }

    public function photoSetting(Request $request)
    {
        $user = Auth::user();

        $user->members_with_photo = $request->members_with_photo;
        $user->vip_members = $request->vip_members;
        $user->blur_photo = $request->blur_photo;
        $user->members_send_request = $request->members_send_request;

        $user->save();

        return $this->successResponse(
            $user,
            'Photo setting updated successfully',
        );
    }

    public function getPartnerSetting()
    {
        $user = Auth::user();

        $userData = LookingFor::where('user_id', $user->id)->first();

        return $this->successResponse(
            $userData,
            'Partner setting fetched successfully',
        );
    }

    public function partnerSetting(Request $request)
    {
        $request->validate([
            'origin' => 'required|string|max:100',
            'gender' => 'required',
            'age_range' => 'required',
            'weight' => 'required|integer|min:30|max:200',
            'height' => 'required|integer|min:100|max:250',
            'religion' => 'required|string|max:50',
            'relationship' => 'required',
            'education' => 'required|string|max:500',
            'rook' => 'required',
            'drinking' => 'required',
            'going_out' => 'required',
            'children' => 'required',
            'location' => 'required',
            'smoke' => 'required',
            'distance_km' => 'required|integer|min:1|max:500',
        ]);

        $user = Auth::user();

        $partner = LookingFor::where('user_id', $user->id)->first();

        $partner->origin = $request->origin;
        $partner->gender = $request->gender;
        $partner->age_range = $request->age_range;
        $partner->weight = $request->weight;
        $partner->height = $request->height;
        $partner->religion = $request->religion;
        $partner->relationship = $request->relationship;
        $partner->education = $request->education;
        $partner->rook = $request->rook;
        $partner->drinking = $request->drinking;
        $partner->going_out = $request->going_out;
        $partner->children = $request->children;
        $partner->location = $request->location;
        $partner->smoke = $request->smoke;
        $partner->distance_km = $request->distance_km;

        $partner->save();

        return $this->successResponse(
            $partner,
            'Partner setting updated successfully',
        );
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        $user->delete();

        return $this->successResponse(
            null,
            'Account deleted successfully',
        );
    }

    public function blockedProfile()
    {
        $user = Auth::user();

        $blockedUsers = $user->blockedUsers()->with('profile:id,user_id,age,location')->get();

        if ($blockedUsers->isEmpty()) {
            return $this->errorResponse('No blocked users found', 404);
        }

        return $this->successResponse($blockedUsers, 'Blocked users fetched successfully');
    }

    public function blockUser($id)
    {
        $auth = Auth::user();

        $user = User::find($id);

        if ($auth->id === $user->id) {
            return $this->errorResponse('You cannot block yourself', 400);
        }

        $alreadyBlocked = $auth->blockedUsers()
            ->where('blocked_id', $user->id)
            ->exists();

        if ($alreadyBlocked) {
            return $this->errorResponse('User already blocked', 409);
        }

        try {
            $auth->blockedUsers()->attach($user->id);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->errorResponse('User already blocked', 409);
        }

        return $this->successResponse(null, 'User blocked successfully');
    }

    public function unblockUser($id)
    {
        $auth = Auth::user();
        $user = User::find($id);
        $isBlocked = $auth->blockedUsers()
            ->where('blocked_id', $user->id)
            ->exists();

        if (! $isBlocked) {
            return $this->errorResponse('This user is not in your blocked list', 404);
        }

        $auth->blockedUsers()->detach($user->id);

        return $this->successResponse(null, 'User unblocked successfully');
    }

    public function profileDetails($id)
    {
        $user = User::find($id);
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return $this->errorResponse('You cannot view your own profile', 400);
        }

        if (! $user) {
            return $this->errorResponse('User not found', 404);
        }

        $isBlocked = $authUser->blockedUsers()
            ->where('blocked_id', $user->id)
            ->exists();

        if (! $isBlocked) {
            return $this->errorResponse('This user is not in your blocked list', 403);
        }

        $user->load(['profile', 'lookingFor']);

        return $this->successResponse(
            $user,
            'Profile details',
        );
    }
}
