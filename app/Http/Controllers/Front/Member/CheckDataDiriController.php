<?php

namespace App\Http\Controllers\Front\Member;

use App\Models\Member as MemberModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckDataDiriController extends Controller
{


    public function index()
    {

        $user = Auth::user();
        $memberId = MemberModel::where('email', $user->email)
            ->select('nama_ktp', 'telp', 'address', 'gender', 'kecamatan', 'birth_date', 'city', 'provinsi', 'desa', 'kodepos')
            ->first();


        if (empty($memberId->telp) || empty($memberId->kodepos) || empty($memberId->address) || empty($memberId->gender) || empty($memberId->birth_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan lengkapi data diri anda terlebih dahulu.'
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Data diri anda sudah lengkap.',
                'data' => $memberId,
            ], 200);
        }
    }
}
