<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RestaurantMap extends Controller
{
    public function getRestaurants($keyword) {
        // Try to get cached response
        $cachedResponse = Cache::get($keyword);

        if($cachedResponse){
            return response()->json($cachedResponse);
        } else {
            $client = new \GuzzleHttp\Client();
            $request = $client->get('https://maps.googleapis.com/maps/api/place/textsearch/json?query=restaurants+in+'.$keyword.'&key=');
            $response = $request->getBody()->getContents();
            $responseData = json_decode($response, true);
        
            // Cache the response for 10 mins
            Cache::put($keyword, $responseData['results'], 600);

            return response()->json($responseData['results']);
        }
    }
}
