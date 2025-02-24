<?php

namespace App\Http\Controllers\Front\Pesan;

use App\Models\PromoUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PesanController extends Controller
{
    public function index()
    {
        return view('page-sdamember.pesan');
    }
}
