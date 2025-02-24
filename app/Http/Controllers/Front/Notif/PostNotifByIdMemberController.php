<?php

namespace App\Http\Controllers\Front\Notif;

use App\Models\PromoUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Member as MemberModel;

class PostNotifByIdMemberController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $userEmail = $user->email;
        $memberId = MemberModel::where('email', $userEmail)->first()->id;
        $notificationId =  $request->id;

        $notification = Notification::where('id', $notificationId)
            ->where('member_id', $memberId)
            ->where('notification_status', 'Unread')
            ->first();




        $promoUser = PromoUser::where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
        $jumlahUnreadPromo = $promoUser->where('promo_status', 'Unread')->count();



        if ($notification) {
            $notification->notification_status = 'Read';
            $notification->save();

            $notifikasi = Notification::where('member_id', $memberId)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();
            $jumlahUnread = $notifikasi->where('notification_status', 'Unread')->count();

            $totalNotif = $jumlahUnread + $jumlahUnreadPromo;

            return response()->json([
                'success' => true,
                'message' => 'Status notifikasi berhasil diubah menjadi Read',
                'notif' => $jumlahUnread,
                'totalNotif' => $totalNotif,
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi tidak ditemukan atau statusnya sudah Read'
            ]);
        }
    }
}
