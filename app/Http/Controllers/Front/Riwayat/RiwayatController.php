<?php

namespace App\Http\Controllers\Front\Riwayat;

use App\Models\PromoUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        return view('page-sdamember.riwayat');
    }
}
