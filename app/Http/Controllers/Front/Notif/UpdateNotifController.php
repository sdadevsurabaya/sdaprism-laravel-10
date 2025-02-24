<?php

namespace App\Http\Controllers\Front\Notif;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateNotifController extends Controller
{
    public function updateStatus($notificationId, $userId)
    {
      
        $notification = Notification::where('id', $notificationId)
            ->where('member_id', $userId)
            ->where('notification_status', 'Unread')
            ->first();


        if ($notification) {
            $notification->notification_status = 'Read';
            $notification->save();

            return response()->json([
                'success' => true,
                'message' => 'Status notifikasi berhasil diubah menjadi Read'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi tidak ditemukan atau statusnya sudah Read'
            ]);
        }
    }
}
