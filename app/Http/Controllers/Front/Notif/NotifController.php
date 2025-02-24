<?php

namespace App\Http\Controllers\Front\Notif;

use App\Models\PromoUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Member as MemberModel;

class NotifController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userEmail = $user->email;
        $memberId = MemberModel::where('email', $userEmail)->first()->id;

        $notif = Notification::select('id', 'notification_title as title', 'notification_content as content', 'created_at as date', 'notification_status as status')->where('member_id', $memberId)
            ->orderBy('created_at', 'desc')->take(20)->get()->toArray();


        // dd($notif);
        $promo = PromoUser::select('onepoint_promo_users.id', 'promo_name as title', 'promo_desc  as content', 'onepoint_promo_users.created_at as date', 'promo_status as status')->join('onepoint_promo_notif', 'onepoint_promo_users.promo_notif_id', '=', 'onepoint_promo_notif.id')
            ->where('onepoint_promo_users.member_id', $memberId)->orderBy('onepoint_promo_users.created_at', 'desc')->take(20)->get();
        return view('page-sdamember.notif', compact('notif', 'promo'));
    }
}

//jika kondisi promo global
//  ->orWhereNull('member_id')
//             ->orWhere('member_id', '')