<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationConteoller extends Controller
{
    use ApiResponse;

    public function getReceivedNotifications()
    {
        $user = Auth::user();

        $data = $user->unreadNotifications()
            ->where('data->data->type', 'like')
            ->latest()
            ->get()
            ->map(function ($notification) {
                return [
                    'notification_id' => $notification->id,
                    'type' => $notification->data['data']['type'],
                    'from_user' => $notification->data['data']['from_user'],
                    'created_at' => $notification->created_at,
                ];
            });

        return $this->successResponse(
            $data,
            'User Received Like Notifications'
        );
    }

    public function userLike(Request $request, $id)
    {
        $fromUser = Auth::user();

        if (! $fromUser) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $toUser = User::findOrFail($id);

        $like = Like::firstOrCreate([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
        ]);

        if (! $like->wasRecentlyCreated) {
            return $this->errorResponse('Already liked', 409);
        }

        $user = User::with('profile')->find($fromUser->id);

        $toUser->notify(new UserNotification([
            'type' => 'like',
            'from_user' => [
                'id' => $user->id,
                'fname' => $user->fname,
                'lname' => $user->lname,
                'photo' => $user->photo,
                'is_online' => $user->isOnline(),
                'profile' => [
                    'age' => $user->profile->age ?? null,
                    'language' => $user->profile->language ?? null,
                    'location' => $user->profile->location ?? null,
                    'about' => $user->profile->about_me ?? null,
                ],
            ],
        ], 'User liked your profile'));

        return $this->successResponse($like, 'Like sent successfully');
    }
}
