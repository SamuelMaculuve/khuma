<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InstanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.instance.create');
    }
    public function connectShow()
    {
        return view('admin.instance.connect.show');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function connect(Request $request)
    {


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://free.uazapi.com/instance/connect",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'phone' => '258848293580'
            ]),
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json",
                "token: d6ea651f-edeb-4660-88fc-16735e4d4475"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $data = json_decode($response, true);
        }
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'admintoken' => 'ZaW1qwTEkuq7Ub1cBUuyMiK5bNSu3nnMQ9lh7klElc2clSRV8t'
            ])->post( 'https://free.uazapi.com/instance/init', [
                'name' => 'khuma-'.$company->name,
                'systemName' => 'khuma',
                'fingerprintProfile' => 'chrome',
                'browser' => 'chrome'
            ]);

            if ($response->successful()) {

                $instance = new Instance();
                $data = $response->json();

                $instance->user_id = Auth::user()->id;
                $instance->token = $data['token'] ?? null;
                $instance->status = $data['instance']['status'] ?? null;
                $instance->profilePic = $data['instance']['profilePicUrl'] ?? null;
                $instance->isBusiness = $data['instance']['isBusiness'] ?? null;
                $instance->profileName = $data['instance']['profileName'] ?? null;
                $instance->name = $data['instance']['name'] ?? null;
                $instance->info = $data['info'] ?? null;
                $instance->save();

            } else {
                Log::info('Erro ao conectar: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::info('Erro: ' . $e->getMessage());
        }

        return  redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Instance $instance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instance $instance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instance $instance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instance $instance)
    {
        //
    }
}
