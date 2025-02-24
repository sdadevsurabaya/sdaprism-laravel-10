<?php

namespace App\Http\Controllers\Front\Profil;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChangeNameProfilController extends Controller
{
    public function index(Request $request)
    {

        $users = Auth::user();
        $id = $users->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 200);
        }

        $namefile = $request->input('name');
        $user = User::find($id);

        if ($file = $request->file('imageFile')) {
            // Pastikan bahwa file yang diunggah adalah gambar
            if ($file->isValid() && ($file->getMimeType() === 'image/jpeg' || $file->getMimeType() === 'image/png')) {
                $timestamp = now()->timestamp;
                $extension = $file->getClientOriginalExtension();
                $name = $id . "_" . $timestamp . "." . $extension;

                // Hapus gambar lama jika ada
                if (!empty($user->image) && file_exists(public_path('images/users/' . $user->image))) {
                    unlink(public_path('images/users/' . $user->image));
                }

                // Pindahkan file ke direktori yang sesuai
                $file->move(public_path('images/users'), $name);
                $user->image = $name;
            } else {
                // Handle jika file bukan gambar
                return response()->json([
                    "success" => false,
                    "message" => "Invalid file format"
                ]);
            }
        } else {
            $name = '';
        }


        $user->name = $namefile;
        $user->save();
        if ($name) {

            $fullUrl = url('/') . '/images/users/' . $name;
        } else {
            $fullUrl = '';
        }

        return response()->json([
            "success" => true,
            "message" => "Profile successfully updated",
            "name" => $namefile,
            "file" => $fullUrl
        ]);
    }
}
