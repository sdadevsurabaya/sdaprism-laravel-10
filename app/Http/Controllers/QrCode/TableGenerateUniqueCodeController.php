<?php

namespace App\Http\Controllers\QrCode;

use App\Http\Controllers\Controller;
use App\Models\UniqueCodeMember;
use Illuminate\Http\Request;

class TableGenerateUniqueCodeController extends Controller
{
    public function index()
    {
      

        $data = UniqueCodeMember::with('member.user')->orderBy("id", "DESC")->get();
        return response()->json([
            'success' => true,
            'message' => 'Success Get Data',
            'data' => $data,
        ]);
    }
}
