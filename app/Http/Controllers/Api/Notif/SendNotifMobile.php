<?php

namespace App\Http\Controllers\Api\Notif;

use App\Services\FCMService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;

class SendNotifMobile extends Controller
{
    public function index(Request $request)
    {
        try {
             $user = JWTAuth::parseToken()->authenticate();
            //  dd();
            $data =  FCMService::send(
                $user->token_firebase,
                [
                    'title' => 'tes2',
                    'body' => 'your body oke jam 14.57',
                ]
            );

            return $data;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 200);
        }
    }

    public function sendNotif()
    {
         $user = JWTAuth::parseToken()->authenticate();
        $firebaseToken = $user->token_firebase;

 $SERVER_API_KEY = env('FCM_SERVER_KEY');

        $data = [
            "to" => $firebaseToken,
            "notification" => [
                "title" => 'tes3',
                "body" => 'tesssss',
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        return  $response;
    }
}
