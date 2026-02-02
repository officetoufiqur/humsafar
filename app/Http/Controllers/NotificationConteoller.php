<?php

namespace App\Http\Controllers;

use App\Models\Connect;
use App\Models\Like;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationConteoller extends Controller
{
    use ApiResponse;

    public function getLikeNotifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->where('data->data->type', 'like')
            ->latest()
            ->get();

        $unreadCount = $user->unreadNotifications()
            ->where('data->data->type', 'like')
            ->count();

        $data = $notifications->map(function ($notification) {
            return [
                'notification_id' => $notification->id,
                'type' => 'like',
                'from_user' => $notification->data['data']['from_user'] ?? null,
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ];
        });

        $user->unreadNotifications()
            ->where('data->data->type', 'like')
            ->update(['read_at' => now()]);

        return $this->successResponse(
            [
                'count' => $unreadCount,
                'notifications' => $data,
            ],
            'User received like notifications'
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

        Connect::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'status' => 'pending',
        ]);

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

    public function userConnect(Request $request, $id)
    {
        $fromUser = Auth::user();
        if (! $fromUser) {
            return $this->errorResponse('Unauthorized', 401);
        }

        if ($fromUser->id == $id) {
            return $this->errorResponse('You cannot connect with yourself', 422);
        }

        $toUser = User::findOrFail($id);

        $userA = min($fromUser->id, $toUser->id);
        $userB = max($fromUser->id, $toUser->id);

        $connect = Connect::where([
            'from_user_id' => $userA,
            'to_user_id' => $userB,
        ])->first();

        if ($connect) {
            if ($connect->status === 'pending' && $connect->from_user_id === $toUser->id) {
                $connect->update(['status' => 'accepted']);

                return $this->successResponse($connect, 'Connection accepted');
            }

            return $this->errorResponse('Connection already exists', 409);
        }

        $connect = Connect::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'status' => 'pending',
        ]);

        $fromUser->load('profile');
        $toUser->load('profile');

        $fromUserName = trim($fromUser->fname.' '.$fromUser->lname);

        $toUser->notify(new UserNotification([
            'type' => 'connect_received',
            'from_user' => [
                'id' => $fromUser->id,
                'fname' => $fromUser->fname,
                'lname' => $fromUser->lname,
                'photo' => $fromUser->photo,
                'is_online' => $fromUser->isOnline(),
                'profile' => [
                    'age' => $fromUser->profile->age ?? null,
                    'language' => $fromUser->profile->language ?? null,
                    'location' => $fromUser->profile->location ?? null,
                    'about' => $fromUser->profile->about_me ?? null,
                ],
            ],
        ], "You received a request from {$fromUserName}"));

        $fromUser->notify(new UserNotification([
            'type' => 'connect_sent',
            'to_user' => [
                'id' => $toUser->id,
                'fname' => $toUser->fname,
                'lname' => $toUser->lname,
                'photo' => $toUser->photo,
                'is_online' => $toUser->isOnline(),
                'profile' => [
                    'age' => $toUser->profile->age ?? null,
                    'language' => $toUser->profile->language ?? null,
                    'location' => $toUser->profile->location ?? null,
                    'about' => $toUser->profile->about_me ?? null,
                ],
            ],
        ], "You sent a request to {$toUser->fname} {$toUser->lname}"));

        return $this->successResponse($connect, 'Connect request sent');
    }

    public function getConnectNotifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->where('data->data->type', 'connect_received')
            ->latest()
            ->get();

        $unreadCount = $user->unreadNotifications()
            ->where('data->data->type', 'connect_received')
            ->count();

        $data = $notifications->map(function ($notification) {
            return [
                'notification_id' => $notification->id,
                'message' => $notification->data['message'] ?? null,
                'type' => 'connect_received',
                'from_user' => $notification->data['data']['from_user'] ?? null,
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ];
        });

        $user->unreadNotifications()
            ->where('data->data->type', 'connect_received')
            ->update(['read_at' => now()]);

        return $this->successResponse(
            [
                'count' => $unreadCount,
                'notifications' => $data,
            ],
            'User connect request notifications'
        );
    }

    public function getSendNotifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->where('data->data->type', 'connect_sent')
            ->latest()
            ->get();

        $unreadCount = $user->unreadNotifications()
            ->where('data->data->type', 'connect_sent')
            ->count();

        $data = $notifications->map(function ($notification) {
            return [
                'notification_id' => $notification->id,
                'message' => $notification->data['message'] ?? null,
                'type' => 'connect_sent',
                'to_user' => $notification->data['data']['to_user'] ?? null,
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ];
        });

        $user->unreadNotifications()
            ->where('data->data->type', 'connect_sent')
            ->update(['read_at' => now()]);

        return $this->successResponse(
            [
                'count' => $unreadCount,
                'notifications' => $data,
            ],
            'User sent connect notifications'
        );
    }

    public function userAccept(Request $request, $connectId)
    {
        $user = Auth::user();

        $connect = Connect::with(['fromUser.profile'])
            ->where('from_user_id', $connectId)
            ->where('to_user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (! $connect) {
            return $this->errorResponse('Connection not found or already handled', 404);
        }

        $connect->update(['status' => 'accepted']);

        $sender = $connect->fromUser;
        $receiverName = trim($user->fname.' '.$user->lname) ?: 'Someone';

        $sender->notify(new UserNotification([
            'type' => 'connect_accepted',
            'accepted' => true,
            'form_user' => [
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
        ], "{$receiverName} accepted your connection request"));

        return $this->successResponse($connect, 'Connection accepted');
    }

    public function getReceivedNotifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->where('data->data->type', 'connect_accepted')
            ->latest()
            ->get();

        $unreadCount = $user->unreadNotifications()
            ->where('data->data->type', 'connect_accepted')
            ->count();

        $data = $notifications->map(function ($notification) {
            return [
                'notification_id' => $notification->id,
                'type' => 'connect_accepted',
                'from_user' => $notification->data['data']['by_user'] ?? null,
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ];
        });

        $user->unreadNotifications()
            ->where('data->data->type', 'connect_accepted')
            ->update(['read_at' => now()]);

        return $this->successResponse(
            [
                'count' => $unreadCount,
                'notifications' => $data,
            ],
            'Accepted connection notifications'
        );
    }

    public function userDecline(Request $request, $connectId)
    {
        $user = Auth::user();

        $connect = Connect::with(['fromUser.profile'])
            ->where('from_user_id', $connectId)
            ->where('to_user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (! $connect) {
            return $this->errorResponse('Connection not found or already handled', 404);
        }

        $connect->update(['status' => 'rejected']);

        $sender = $connect->fromUser;
        $receiverName = trim($user->fname.' '.$user->lname) ?: 'Someone';

        $sender->notify(new UserNotification([
            'type' => 'connect_rejected',
            'accepted' => true,
            'by_user' => [
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
        ], "You received a deleted from {$receiverName}"));

        return $this->successResponse($connect, 'Connection rejected');
    }

    public function getDeclinedNotifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->where('data->data->type', 'connect_rejected')
            ->latest()
            ->get();

        $unreadCount = $user->unreadNotifications()
            ->where('data->data->type', 'connect_rejected')
            ->count();

        $data = $notifications->map(function ($notification) {
            return [
                'notification_id' => $notification->id,
                'message' => $notification->data['message'] ?? null,
                'type' => 'connect_rejected',
                'by_user' => $notification->data['data']['by_user'] ?? null,
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ];
        });

        $user->unreadNotifications()
            ->where('data->data->type', 'connect_rejected')
            ->update(['read_at' => now()]);

        return $this->successResponse(
            [
                'count' => $unreadCount,
                'notifications' => $data,
            ],
            'Declined connection notifications'
        );
    }

     public function adminNotifications()
    {
        $user = User::role('admin')->first();

        $notifications = $user->notifications()
        ->where('data->data->type', 'admin')
        ->latest()
        ->get();

        $unreadCount = $user->unreadNotifications()
            ->where('data->data->type', 'admin')
            ->count();

        $data = $notifications->map(function ($notification) {
            return [
                'notification_id' => $notification->id,
                'message' => $notification->data['message'] ?? null,
                'user' => $notification->data['data']['user'] ?? null,
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ];
        });

        $user->unreadNotifications()
            ->where('data->data->type', 'admin')
            ->update(['read_at' => now()]);

        return $this->successResponse(
            [
                'count' => $unreadCount,
                'notifications' => $data,
            ],
            'Declined connection notifications'
        );
    }
}
