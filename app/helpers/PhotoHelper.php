<?php
namespace ADP\Helpers;

use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class PhotoHelper
{

    public static function uploadBase64($base64, $nameFile, $storage){
        $files = base64_decode(explode(",", $base64)[1]);
        
        $format = explode(";", explode("/", explode(",", $base64)[0])[1])[0];
        $defineImg = $nameFile.'.'.$format;
        Storage::disk($storage)->put($defineImg, $files);

        $client = new Client();
        $response = $client->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                'authorization' => 'Client-ID '.env('IMGUR_CLIENT_ID'),
                'content-type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'image' => base64_encode(Storage::disk($storage)->get($defineImg))
            ],
        ]);

        $img = data_get(response()->json(json_decode(($response->getBody()->getContents())))->getData(), 'data.link');
        Storage::disk($storage)->delete($defineImg);

        return $img;
    }

    public static function uploadImg($file, $nameFile, $storage){
        $base64 = "data:image/png;base64,".base64_encode(file_get_contents($file));
        $files = base64_decode(explode(",", $base64)[1]);
        $format = explode(";", explode("/", explode(",", $base64)[0])[1])[0];
        $defineImg = $nameFile.'.'.$format;
        Storage::disk($storage)->put($defineImg, $files);

        $client = new Client();
        $response = $client->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                'authorization' => 'Client-ID '.env('IMGUR_CLIENT_ID'),
                'content-type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'image' => base64_encode(Storage::disk($storage)->get($defineImg))
            ],
        ]);

        $img = data_get(response()->json(json_decode(($response->getBody()->getContents())))->getData(), 'data.link');
        Storage::disk($storage)->delete($defineImg);

        return $img;
    }

}