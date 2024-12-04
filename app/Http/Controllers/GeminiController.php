<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

// use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


////////////////////////
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Gemini\Client;


class GeminiController extends Controller
{
    public function index(Request $request){

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=AIzaSyD-qkyk4xEnP_VDL5OlcZGVRWV0vxC0tjc', [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $request->text
                        ]
                    ]
                ]
            ]
        ]);

            // You can access the response body like this
        $responseData = $response->json();
        // $text = $responseData['candidates'][0]['content']['parts'][0]['text'];


        return  $responseData;
    }

    public function generateContentWithImages(Request $request)
    {


    // Validate the form data
    $request->validate([
        'image' => 'required|image|mimes:png,jpeg,webp,heic,heif|max:4096', // Adjust the maximum file size as needed
        'text' => 'required|string',
    ]);


    // Retrieve the submitted image and text
    $image = $request->file('image');
    $text = $request->input('text');

    // Process the image file to get its base64 representation
    $imageData = base64_encode(file_get_contents($image->path()));

    // Use the retrieved image data and text in your API call
    $result = Gemini::geminiProVision()
        ->generateContent([
            $text,
            new Blob(
                mimeType: MimeType::IMAGE_JPEG,
                data: $imageData
            )
        ]);

        // dd($result);
        $textResult = $result->text();

        return view('output-gemini', compact('textResult'));
    }
}
