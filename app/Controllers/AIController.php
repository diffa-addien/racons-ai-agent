<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;

// use GuzzleHttp\Client;
// use GuzzleHttp\Psr7\Response;

class AIController extends BaseController
{
  use ResponseTrait;

  public function index()
  {
    return view('chat/home');
  }

  // public function generateAIText()
  // {
  //   $client = new Client();
  //   $apiKey = getenv('AI_API_KEY'); // Ambil API key dari .env

  //   // $model = 'gemini-2.0-flash'; // Ganti dengan model yang sesuai
  //   // Ambil data JSON dari request
  //   $json = $this->request->getJSON();
  //   if (!$json || !isset($json->message)) {
  //     return $this->failValidationErrors('Pesan tidak ditemukan dalam request.');
  //   }

  //   $message = $json->message;

  //   $data = [
  //     'contents' => [
  //       [
  //         'role' => "user",
  //         'parts' => [
  //           "text" => $message
  //         ]
  //       ]
  //     ],
  //   ];

  //   try {
  //     $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey, [ // Ganti dengan endpoint yang benar
  //       'json' => $data,
  //       'system_instruction' => "singkat saja",
  //     ]);

  //     $result = json_decode($response->getBody(), true);
  //     // $resulText = $response->getHeader('Content-Length')[0];
  //     // Kembalikan respons JSON
  //     // return $this->respond([
  //     //   "status" => $response->getStatusCode(),
  //     //   "text" => $result
  //     // ]);

  //     // Proses respons dan tampilkan hasilnya
  //     if (isset($result['candidates']) && !empty($result['candidates'])) {
  //       $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];
  //       return $this->response->setJSON(['status' => 'success', 'text' => $generatedText]);
  //     } else {
  //       return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghasilkan teks.']);
  //     }
  //   } catch (\Exception $e) {
  //     return $this->response->setJSON(['status' => 'error', 'message er' => $e->getMessage()]);
  //   }
  // }


  public function geminiAIText()
  {
    // Ambil data JSON dari request
    $json = $this->request->getJSON();
    if (!$json || !isset($json->message)) {
      return $this->failValidationErrors('Pesan tidak ditemukan dalam request.');
    }
    $message = $json->message;

    $ruleInstruction = 'Follow this rules to answer chat = {
      "Identity": "Your name is Racons AI",
      "length": "Short"
    }';
    die;
    try {
      $client = new Client('AIzaSyBqwWW4kkkHaiESgbEQKlRxyZxOieFpoe0');
      // $response = $client->generativeModel(ModelName::GEMINI_PRO)->generateContent(
      //   new TextPart($message),
      // );
      $response = $client->withV1BetaVersion()
        ->generativeModel(ModelName::GEMINI_1_5_FLASH_8B)
        ->withSystemInstruction($ruleInstruction)
        ->generateContent(
          new TextPart($message),
        );

      $resulText = $response->text();
      // Kembalikan respons JSON
      return $this->respond([$resulText]);
    } catch (\Exception $e) {
      // Tangani error
      return $this->failServerError($e->getMessage());
      return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
        ->setJSON(['error' => $e->getMessage()]);
    }
  }
}
