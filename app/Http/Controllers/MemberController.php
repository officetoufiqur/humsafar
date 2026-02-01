<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\LookingFor;
use App\Models\Profile;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = User::with(['profile', 'lookingFor']);

        if ($status) {
            $allowedStatuses = ['active', 'inactive', 'blocked', 'unblocked'];

            if (! in_array($status, $allowedStatuses)) {
                return $this->errorResponse('Invalid status value', 400);
            }

            $query->where('status', $status);
        }

        $members = $query->get();

        if ($members->isEmpty()) {
            return $this->errorResponse('No members found', 404);
        }

        $total = $members->count();
        $totalActive = $members->where('status', 'active')->count();
        $totalInactive = $members->where('status', 'inactive')->count();
        $totalBlocked = $members->where('status', 'blocked')->count();

        $activePercentage = ($totalActive / $total) * 100;
        $inactivePercentage = ($totalInactive / $total) * 100;
        $blockedPercentage = ($totalBlocked / $total) * 100;

        $data = [
            'members' => $members,
            'statistics' => [
                'total' => $total,
                'active' => $totalActive,
                'inactive' => $totalInactive,
                'blocked' => $totalBlocked,
                'percentages' => [
                    'active' => $activePercentage,
                    'inactive' => $inactivePercentage,
                    'blocked' => $blockedPercentage,
                ],
            ],
        ];

        return $this->successResponse($data, 'Members fetched successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = new User;

        $file = null;
        if ($request->hasFile('photo')) {
            $file = FileUpload::storeFile($request->file('photo'), 'uploads/users');
        }

        $user->display_name = $request->display_name;
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->dob = $request->dob;
        $user->email = $request->email;
        $user->photo = $file;
        $user->password = Hash::make($request->password);
        $user->save();

        // Create profile
        $profile = new Profile;

        $profile->user_id = $user->id;
        $profile->origin = $request->origin;
        $profile->gender = $request->gender;
        $profile->age = $request->age;
        $profile->relationship = $request->relationship;
        $profile->children = $request->children;
        $profile->religion = $request->religion;
        $profile->looking_for = $request->looking_for;
        $profile->about_me = $request->about_me;
        $profile->edu_primary = $request->edu_primary;
        $profile->edu_secondary = $request->edu_secondary;
        $profile->edu_qualification = $request->edu_qualification;
        $profile->experience = $request->experience;
        $profile->certifications = $request->certifications;
        $profile->department = $request->department;
        $profile->position = $request->position;
        $profile->personal_attitude = $request->personal_attitude;
        $profile->smoke = $request->smoke;
        $profile->drinking = $request->drinking;
        $profile->going_out = $request->going_out;
        $profile->exercise = $request->exercise;
        $profile->diet = $request->diet;
        $profile->pets = $request->pets;
        $profile->travel = $request->travel;
        $profile->social_media = $request->social_media;
        $profile->work_life_balance = $request->work_life_balance;
        $profile->night_life = $request->night_life;
        $profile->hobby = $request->hobby;
        $profile->sports = $request->sports;
        $profile->music = $request->music;
        $profile->cooking = $request->cooking;
        $profile->reading = $request->reading;
        $profile->tv_shows = $request->tv_shows;
        $profile->mother_tongue = $request->mother_tongue;
        $profile->known_language = $request->known_language;
        $profile->country = $request->country;
        $profile->state = $request->state;
        $profile->city = $request->city;
        $profile->weight = $request->weight;
        $profile->height = $request->height;
        $profile->eye_color = $request->eye_color;
        $profile->hair_color = $request->hair_color;
        $profile->body_type = $request->body_type;
        $profile->appearance = $request->appearance;
        $profile->clothing = $request->clothing;
        $profile->intelligence = $request->intelligence;
        $profile->language = $request->language;
        $profile->save();

        // Create LookingFor
        $lookingFor = new LookingFor;

        $lookingFor->user_id = $user->id;
        $lookingFor->looking_origin = $request->looking_origin;
        $lookingFor->looking_gender = $request->looking_gender;
        $lookingFor->looking_height = $request->looking_height;
        $lookingFor->looking_weight = $request->looking_weight;
        $lookingFor->looking_relationship = $request->looking_relationship;
        $lookingFor->looking_religion = $request->looking_religion;
        $lookingFor->looking_education = $request->looking_education;
        $lookingFor->looking_smoke = $request->looking_smoke;
        $lookingFor->looking_drinking = $request->looking_drinking;
        $lookingFor->looking_going_out = $request->looking_going_out;
        $lookingFor->looking_age_range = $request->looking_age_range;
        $lookingFor->looking_country = $request->looking_country;
        $lookingFor->looking_state = $request->looking_state;
        $lookingFor->looking_city = $request->looking_city;
        $lookingFor->save();

        $user = array_merge(
            $user->toArray(),
            $profile->toArray(),
            $lookingFor->toArray()
        );

        return $this->successResponse(
            $user,
            'Member created successfully',
        );
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($request->hasFile('photo')) {
            $file = FileUpload::updateFile($request->file('photo'), 'uploads/users', $user->photo);
            $user->photo = $file;
        }

        $user->display_name = $request->display_name;
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->dob = $request->dob;
        $user->email = $request->email;
        $user->save();

        // Create profile
        $profile = Profile::where('user_id', $user->id)->first();

        $profile->user_id = $user->id;
        $profile->origin = $request->origin;
        $profile->gender = $request->gender;
        $profile->age = $request->age;
        $profile->relationship = $request->relationship;
        $profile->children = $request->children;
        $profile->religion = $request->religion;
        $profile->looking_for = $request->looking_for;
        $profile->about_me = $request->about_me;
        $profile->edu_primary = $request->edu_primary;
        $profile->edu_secondary = $request->edu_secondary;
        $profile->edu_qualification = $request->edu_qualification;
        $profile->experience = $request->experience;
        $profile->certifications = $request->certifications;
        $profile->department = $request->department;
        $profile->position = $request->position;
        $profile->personal_attitude = $request->personal_attitude;
        $profile->smoke = $request->smoke;
        $profile->drinking = $request->drinking;
        $profile->going_out = $request->going_out;
        $profile->exercise = $request->exercise;
        $profile->diet = $request->diet;
        $profile->pets = $request->pets;
        $profile->travel = $request->travel;
        $profile->social_media = $request->social_media;
        $profile->work_life_balance = $request->work_life_balance;
        $profile->night_life = $request->night_life;
        $profile->hobby = $request->hobby;
        $profile->sports = $request->sports;
        $profile->music = $request->music;
        $profile->cooking = $request->cooking;
        $profile->reading = $request->reading;
        $profile->tv_shows = $request->tv_shows;
        $profile->mother_tongue = $request->mother_tongue;
        $profile->known_language = $request->known_language;
        $profile->country = $request->country;
        $profile->state = $request->state;
        $profile->city = $request->city;
        $profile->weight = $request->weight;
        $profile->height = $request->height;
        $profile->eye_color = $request->eye_color;
        $profile->hair_color = $request->hair_color;
        $profile->body_type = $request->body_type;
        $profile->appearance = $request->appearance;
        $profile->clothing = $request->clothing;
        $profile->intelligence = $request->intelligence;
        $profile->language = $request->language;
        $profile->save();

        // Create LookingFor
        $lookingFor = LookingFor::where('user_id', $user->id)->first(); 

        $lookingFor->user_id = $user->id;
        $lookingFor->looking_origin = $request->looking_origin;
        $lookingFor->looking_gender = $request->looking_gender;
        $lookingFor->looking_height = $request->looking_height;
        $lookingFor->looking_weight = $request->looking_weight;
        $lookingFor->looking_relationship = $request->looking_relationship;
        $lookingFor->looking_religion = $request->looking_religion;
        $lookingFor->looking_education = $request->looking_education;
        $lookingFor->looking_smoke = $request->looking_smoke;
        $lookingFor->looking_drinking = $request->looking_drinking;
        $lookingFor->looking_going_out = $request->looking_going_out;
        $lookingFor->looking_age_range = $request->looking_age_range;
        $lookingFor->looking_country = $request->looking_country;
        $lookingFor->looking_state = $request->looking_state;
        $lookingFor->looking_city = $request->looking_city;
        $lookingFor->save();

        $user = array_merge(
            $user->toArray(),
            $profile->toArray(),
            $lookingFor->toArray()
        );

        return $this->successResponse(
            $user,
            'Member update successfully',
        );
    }

    public function statusUpdate($id)
    {
        $user = User::find($id);

        if ($user->status == "active") {
            $user->status = "inactive";
        } else {
            $user->status = "active";
        }

        $user->save();

        return $this->successResponse(
            $user,
            'Member status updated successfully',
        );
    }

    public function view($id)
    {
        $user = User::with('profile', 'lookingFor')->find($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->successResponse(
            $user,
            'Member details',
        );
    }

    public function destroy($id)
    {
        $user = User::find($id);    

        $user->delete();

        return $this->successResponse(
            $user,
            'Member deleted successfully',
        );
    }
}
