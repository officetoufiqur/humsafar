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
            'dob' => 'nullable|date',
            'age' => 'nullable|integer',
            'is_accept' => 'nullable|boolean',
            'is_permission' => 'nullable|boolean',
            'gender' => 'nullable',
            'origin' => 'nullable|string',
            'looking_for' => 'nullable|string',
            'relationship' => 'nullable|string',
            'children' => 'nullable|integer',
            'religion' => 'nullable|string',
            'hair_color' => 'nullable|string',
            'eye_color' => 'nullable|string',
            'body_type' => 'nullable|string',
            'appearance' => 'nullable|string',
            'intelligence' => 'nullable|string',
            'clothing' => 'nullable|string',
            'mother_tongue' => 'nullable|string',
            'known_language' => 'nullable|string',
            'weight' => 'nullable|integer',
            'height' => 'nullable|integer',
            'education' => 'nullable|string',
            'career' => 'nullable|string',
            'about_me' => 'nullable|string',
            'sports' => 'nullable|array',
            'music' => 'nullable|array',
            'cooking' => 'nullable|array',
            'reading' => 'nullable|array',
            'tv_shows' => 'nullable|array',
            'personal_attitude' => 'nullable|array',
            'smoke' => 'nullable',
            'drinking' => 'nullable',
            'going_out' => 'nullable',
            'looking_gender' => 'nullable',
            'looking_origin' => 'nullable|string',
            'looking_relationship' => 'nullable|string',
            'looking_religion' => 'nullable|string',
            'looking_age_range' => 'nullable|string',
            'looking_height' => 'nullable|integer',
            'looking_weight' => 'nullable|integer',
            'looking_education' => 'nullable|string',
            'looking_children' => 'nullable|integer',
            'looking_smoke' => 'nullable',
            'looking_drinking' => 'nullable',
            'looking_going_out' => 'nullable',
            'looking_location' => 'nullable|string',
            'looking_distance_km' => 'nullable|integer',
            'photo' => 'nullable|string',
        ];
    }
}
