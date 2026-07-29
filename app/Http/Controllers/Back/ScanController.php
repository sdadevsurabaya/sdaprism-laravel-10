<?php

namespace App\Http\Controllers\Back;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;

class ScanController extends Controller
{
    public function index()
    {
        ActivityLogger::log('QR Scan', 'Hose Assembly Access', 'Pengguna membuka halaman Hose Assembly');
        return view('back.scan.index');
    }

    public function camera()
    {
        ActivityLogger::log('QR Scan', 'Direct Camera Access', 'Pengguna membuka kamera QR Scanner');
        return view('back.scan.camera');
    }
}
