<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NotificationController extends BaseController
{
    /**
     * Display a listing of notifications and the notification composer.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $notifications = Notification::with('user:id,first_name,last_name,email')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $users = User::whereNull('deleted_at')
            ->where('status', '1')
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get();

        return Inertia::render('Admin/Notification/Index', [
            'rows' => $notifications,
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Broadcast an in-app notification from the admin panel to all users or a specific user.
     */
    public function send(Request $request)
    {
        // Enforce RBAC: Super Admin and Admin only
        $currentUser = Auth::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            abort(403, __('Unauthorized. Only administrators can broadcast notifications.'));
        }

        $validated = $request->validate([
            'audience' => 'required|in:all,specific',
            'user_id' => 'required_if:audience,specific|nullable|exists:users,id',
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
            'type' => 'nullable|string|in:info,announcement,warning,success',
            'url' => 'nullable|string|max:255',
        ]);

        $title = trim($validated['title']);
        $message = trim($validated['message']);
        $url = !empty($validated['url']) ? trim($validated['url']) : '/dashboard';
        $now = now();

        $deliveredCount = 0;

        if ($validated['audience'] === 'specific') {
            $user = User::where('id', $validated['user_id'])->whereNull('deleted_at')->first();
            if (!$user) {
                return back()->with('status', [
                    'type' => 'error',
                    'message' => __('Selected user is inactive or deleted.')
                ]);
            }

            Notification::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'title' => $title,
                'comment' => $message,
                'url' => $url,
                'seen' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $deliveredCount = 1;
        } else {
            // Broadcast to all active users with chunking to avoid memory exhaustion
            User::whereNull('deleted_at')
                ->where('status', '1')
                ->chunk(200, function ($users) use ($title, $message, $url, $now, &$deliveredCount) {
                    $insertData = [];
                    foreach ($users as $user) {
                        $insertData[] = [
                            'uuid' => (string) Str::uuid(),
                            'user_id' => $user->id,
                            'title' => $title,
                            'comment' => $message,
                            'url' => $url,
                            'seen' => false,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    if (!empty($insertData)) {
                        DB::table('notifications')->insert($insertData);
                        $deliveredCount += count($insertData);
                    }
                });
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => $deliveredCount,
                'message' => __(':count notification(s) successfully delivered!', ['count' => $deliveredCount])
            ]);
        }

        return back()->with('status', [
            'type' => 'success',
            'message' => __(':count notification(s) successfully delivered!', ['count' => $deliveredCount])
        ]);
    }
}