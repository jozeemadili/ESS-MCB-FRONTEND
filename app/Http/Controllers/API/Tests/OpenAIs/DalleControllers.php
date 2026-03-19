<?php

namespace App\Http\Controllers\API\Tests\OpenAIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DalleControllers extends Controller
{
    public function test(Request $request)
    {
            // $client = new Client();
            // $response = $client->post('https://api.openai.com/v1/images/generations', [
            //     'headers' => [
            //         'Content-Type' => 'application/json',
            //         'Authorization' => 'Bearer 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',
            //     ],
            //     'json' => [
            //         'model' => 'image-alpha-001',
            //         'prompt' => 'Extract text from this pdf',
            //         'data' => [
            //             'url' => 'https://www.w3docs.com/uploads/media/default/0001/01/540cb75550adf33f281f29132dddd14fded85bfc.pdf',
            //         ],
            //     ],
            // ]);

            $client = new Client();
            $response = $client->post('https://api.openai.com/v1/engines/davinci-codex/completions', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer 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',
                ],
                'json' => [
                    'prompt' => 'Extract text from this pdf',
                    'temperature' => 0.5,
                    'max_tokens' => 100,
                    'stop' => '',
                    'pdf_url' => 'https://www.w3docs.com/uploads/media/default/0001/01/540cb75550adf33f281f29132dddd14fded85bfc.pdf',
                ],
            ]);

$text = $response->getBody()->getContents();
            
            return $response->getBody()->getContents();
    }
}
