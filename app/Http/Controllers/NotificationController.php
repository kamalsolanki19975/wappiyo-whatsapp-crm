<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get the latest notifications and unread count for authenticated user.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'notifications' => [],
                'unread_count' => 0,
            ]);
        }

        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'uuid' => $n->uuid,
                    'title' => $n->title,
                    'comment' => $n->comment,
                    'url' => $n->url ?: '/dashboard',
                    'seen' => (bool) $n->seen,
                    'created_at' => $n->created_at ? $n->created_at->diffForHumans() : 'Just now',
                    'created_at_raw' => $n->created_at ? $n->created_at->toIso8601String() : null,
                ];
            });

        $unreadCount = Notification::where('user_id', $userId)
            ->where('seen', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a specific notification as seen.
     */
    public function markAsRead(Request $request, $id)
    {
        $userId = Auth::id();

        Notification::where('user_id', $userId)
            ->where('id', $id)
            ->update(['seen' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications for the authenticated user as seen.
     */
    public function markAllAsRead(Request $request)
    {
        $userId = Auth::id();

        Notification::where('user_id', $userId)
            ->where('seen', false)
            ->update(['seen' => true]);

        return response()->json(['success' => true]);
    }
}
