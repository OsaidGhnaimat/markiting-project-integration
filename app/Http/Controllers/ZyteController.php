<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZyteController extends Controller
{

        // https://books.toscrape.com/

    public function osman(Request $request){
        $client = Http::withHeaders([
            'Accept-Encoding' => 'gzip',
        ])->withBasicAuth('14251af9d77a4f45a8322424318a201b', '')
          ->post('https://api.zyte.com/v1/extract', [
            'url' => $request->site,
            'httpResponseBody' => true,
        ]);

        // Check if the request was successful
        if ($client->successful()) {
            $response = $client->json();
            $http_response_body = base64_decode($response['httpResponseBody']);

            // Clean up HTML by removing unnecessary comments and spaces
            $cleaned_response_body = preg_replace('/<!--(.*?)-->/', '', $http_response_body);
            $cleaned_response_body = preg_replace('/\s+/', ' ', $cleaned_response_body);

            // Decode HTML entities
            $decoded_response_body = html_entity_decode($cleaned_response_body);

            // Build formatted response
            $formatted_response = [
                'url' => $response['url'],
                'statusCode' => $response['statusCode'],
                'httpResponseBody' => $decoded_response_body
            ];

            return $formatted_response;
        } else {
            // Handle unsuccessful request
            return response()->json(['error' => 'Failed to fetch data.'], $client->status());
        }
    }





    // public function osman(Request $request){
    //     $client = Http::withHeaders([
    //         'Accept-Encoding' => 'gzip',
    //         'Authorization'=> 'Basic Zm9vOg=='
    //     ])->withBasicAuth('14251af9d77a4f45a8322424318a201b', '')
    //       ->post('https://api.zyte.com/v1/extract', [
    //         'url' => $request->site,
    //         'httpResponseBody' => true,
    //         'screenshot' => 'string',
    //         'article' => true,
    //         'articleList' => true,
    //         'articleNavigation' => true,
    //         'jobPosting' => true,
    //         'product' => true,
    //         'productList' => true,
    //         'productNavigation' => true
    //     ]);

    //     // Check if the request was successful
    //     if ($client->successful()) {
    //         $response = $client->json();

    //         // Build formatted response
    //         $formatted_response = [
    //             'url' => $response['url'],
    //             'statusCode' => $response['statusCode'],
    //             'httpResponseBody' => $response['httpResponseBody'],
    //             'httpResponseHeaders' => $response['httpResponseHeaders'],
    //             'browserHtml' => $response['browserHtml'],
    //             'screenshot' => $response['screenshot'],
    //             'article' => $response['article'],
    //             'articleList' => $response['articleList'],
    //             'articleNavigation' => $response['articleNavigation'],
    //             'jobPosting' => $response['jobPosting'],
    //             'product' => $response['product'],
    //             'productList' => $response['productList'],
    //             'productNavigation' => $response['productNavigation'],
    //             'echoData' => $response['echoData'],
    //             'jobId' => $response['jobId'],
    //             'actions' => $response['actions'],
    //             'responseCookies' => $response['responseCookies']
    //         ];

    //         return $formatted_response;
    //     } else {
    //         // Handle unsuccessful request
    //         return response()->json(['error' => 'Failed to fetch data.'], $client->status());
    //     }
    // }


}
