<?php

namespace App\Http\Controllers\Back;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;

class ScanController extends Controller
{
    public function index()
    {
        ActivityLogger::log('QR Scan', 'QR Access', 'Pengguna membuka halaman QR Scanner');
        return view('back.scan.index');
    }
}
