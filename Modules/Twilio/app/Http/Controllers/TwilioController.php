<?php

namespace Modules\Twilio\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwilioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('twilio::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('twilio::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('twilio::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('twilio::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}

    public function sendSms()
    {
        $sid = env('ACCOUNT_SID');
        $token = env('AUTH_TOKEN');
        $from = '+9779849375973'; // Your Twilio number
        $to = '+9779741665230';   // Receiver's number
        $body = 'hello CodeHunger';

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        $data = http_build_query([
            'To' => $to,
            'From' => $from,
            'Body' => $body,
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERPWD, "{$sid}:{$token}");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return "Curl error: " . $error_msg;
        }

        curl_close($ch);

        echo $response;
        return;
    }
}
