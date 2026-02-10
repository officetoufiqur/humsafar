<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Trait\ApiResponse;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    use ApiResponse;

    public function translations($lang)
    {
        $translations = cache()->remember("api_trans_$lang", 3600, function () use ($lang) {
            return Translation::where('lang_code', $lang)
                ->pluck('value', 'key');
        });

        return $this->successResponse($translations, 'Translations retrieved successfully.');
    }

    public function index()
    {
        $languages = Language::all();

        if ($languages->isEmpty()) {
            return $this->errorResponse('No languages found.', 404);
        }

        return $this->successResponse($languages, 'Languages retrieved successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:languages,code',
            'status' => 'boolean',
        ]);

        $language = Language::create([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status ?? true,
        ]);

        return $this->successResponse($language, 'Language created successfully.', 201);
    }

    public function edit($code)
    {
        $current = Translation::where('lang_code', $code)
            ->pluck('value', 'key');

        return $this->successResponse( $current, 'Language translation data retrieved successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'lang' => 'required|string|exists:languages,code',
            'translations' => 'required|array',
        ]);

        $language = Language::where('code', $request->lang)->first();

        if (! $language) {
            return $this->errorResponse('Invalid language code.', 404);
        }

        foreach ($request->translations as $key => $value) {
            Translation::updateOrCreate(
                [
                    'lang_code' => $request->lang,
                    'key' => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        }

        cache()->forget("api_trans_{$request->lang}");

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $language = Language::find($id);

        if (! $language) {
            return $this->errorResponse('Language not found.', 404);
        }

        if ($language->status) {
            return $this->errorResponse(
                'Active language cannot be deleted. Disable it first.',
                422
            );
        }

        DB::transaction(function () use ($language) {
            Translation::where('lang_code', $language->code)->delete();
            $language->delete();
            cache()->forget("api_trans_{$language->code}");
        });

        return $this->successResponse(null, 'Language deleted successfully.');
    }
}
