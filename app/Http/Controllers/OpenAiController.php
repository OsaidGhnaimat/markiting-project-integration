<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class OpenAiController extends Controller
{
    public function index(){

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4-vision-preview',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'translate this message to arabic "hi how are you , can i go with you"'
                    ]
                ],
                'temperature' => 0.7,
            ]);

        $textResult = $response;

        return view('openAiOutput', compact('textResult'));
    }
}
