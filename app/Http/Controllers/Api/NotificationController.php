<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // GET /api/user/notifications
    public function index(Request $request)
    {
        $user = $request->user();
        $limit = $request->input('limit', 20);
        $query = UserNotification::where('user_id', $user->id)
            ->whereNull('archived_at')
            ->orderBy('created_at', 'desc');
        return $query->limit($limit)->get();
    }

    // POST /api/user/notifications/{id}/read
    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = UserNotification::where('user_id', $user->id)->findOrFail($id);
        $notification->read_at = now();
        $notification->save();
        return response()->json(['message' => 'Notificación marcada como leída']);
    }

    // POST /api/user/notifications/{id}/archive
    public function archive(Request $request, $id)
    {
        $user = $request->user();
        $notification = UserNotification::where('user_id', $user->id)->findOrFail($id);
        $notification->archived_at = now();
        $notification->save();
        return response()->json(['message' => 'Notificación archivada']);
    }

    // POST /api/user/notifications/archive-all
    public function archiveAll(Request $request)
    {
        $user = $request->user();
        UserNotification::where('user_id', $user->id)
            ->whereNull('archived_at')
            ->update(['archived_at' => now()]);
        return response()->json(['message' => 'Todas las notificaciones archivadas']);
    }
}
