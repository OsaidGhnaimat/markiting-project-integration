<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class gpt4ChatController extends Controller
{
    public function index(){
        return view('gpt4-form');
    }

    public function inegration(Request $request){

            // Define the data for the request
        $data = [
            "model" => "gpt-4-vision-preview",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => $request->text
                        ],
                        [
                            "type" => "image_url",
                            "image_url" => [
                                "url" => $request->image
                            ]
                        ]
                    ]
                ]
            ],
            "max_tokens" => 300
        ];

        // Make the POST request to OpenAI API
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . env("OPENAI_API_KEY")
        ])->post("https://api.openai.com/v1/chat/completions", $data);

        // Get the response body
        $result = $response->json();

        // Handle the result as needed
        echo $result['choices'][0]['message']['content'];
    }
}
