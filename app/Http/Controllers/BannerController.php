<?php

namespace App\Http\Controllers;

use App\Models\NewsLetter;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponse;

    public function newsLetters(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'emails' => 'required|string',
        ]);

        $emails = array_unique(array_map('trim', explode(',', $request->emails)));

        foreach ($emails as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'message' => "Invalid email: $email",
                ], 422);
            }
        }

        $newsletter = NewsLetter::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'emails' => $emails,
        ]);

        return $this->successResponse($newsletter, 'News Letter created successfully');
    }
}
