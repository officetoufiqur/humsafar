<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dob' => 'required|date',
            'age' => 'required|integer',
            'is_accept' => 'required|boolean',
            'is_permission' => 'required|boolean',
            'gender' => ['required', Rule::in(['male','female','other'])],
            'origin' => 'required|string',
            'looking_for' => 'required|string',
            'relationship' => 'required|string',
            'children' => 'required|integer',
            'religion' => 'required|string',
            'hair_color' => 'required|string',
            'eye_color' => 'required|string',
            'body_type' => 'required|string',
            'appearance' => 'required|string',
            'intelligence' => 'required|string',
            'clothing' => 'required|string',
            'mother_tongue' => 'required|string',
            'known_language' => 'required|string',
            'weight' => 'required|integer',
            'height' => 'required|integer',
            'education' => 'required|string',
            'career' => 'required|string',
            'about_me' => 'required|string',
            'sports' => 'required|array',
            'music' => 'required|array',
            'cooking' => 'required|array',
            'reading' => 'required|array',
            'tv_shows' => 'required|array',
            'personal_attitude' => 'required|array',
            'smoke' => ['required', Rule::in(['no','occasionally','yes'])],
            'drinking' => ['required', Rule::in(['no','occasionally','yes'])],
            'going_out' => ['required', Rule::in(['never','sometimes','often'])],
            'membership_name' => 'required|string',
            'membership_amount' => 'required|numeric',
            'looking_gender' => ['required', Rule::in(['male','female','other'])],
            'looking_origin' => 'required|string',
            'looking_relationship' => 'required|string',
            'looking_religion' => 'required|string',
            'looking_age_range' => 'required|string',
            'looking_height' => 'required|integer',
            'looking_weight' => 'required|integer',
            'looking_education' => 'required|string',
            'looking_children' => 'required|integer',
            'looking_smoke' => ['required', Rule::in(['no','occasionally','yes'])],
            'looking_drinking' => ['required', Rule::in(['no','occasionally','yes'])],
            'looking_going_out' => ['required', Rule::in(['never','sometimes','often'])],
            'looking_location' => 'required|string',
            'looking_distance_km' => 'required|integer',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ];
    }
}
