<?php

namespace App\Http\Controllers\Front\Member;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostProfilRequiredController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $userId = Auth::user();
        $user = User::find($userId->id);

        // $user->name = $request->nama_lengkap;
        // $user->save();

        $member = $user->member;
        $member->telp = $request->telp;
        $member->birth_date = $request->birth_date;
        $member->nama_ktp = $request->nama_ktp;
        $member->gender = $request->gender;
        $member->address = $request->address;
        $member->provinsi = $request->provinsi;
        $member->city = $request->kota;
        $member->kecamatan = $request->kecamatan;
        $member->desa = $request->kelurahan;
        $member->kodepos = $request->kodepos;
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully' . $userId->id,
        ]);
    }
}
