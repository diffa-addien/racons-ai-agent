<?php

namespace App\Controllers\ClientAgent;

use App\Controllers\BaseController;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;

class GeminiAgent extends BaseController
{
  use ResponseTrait;

  public function index(): object
  {
    return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
  }

  public function responseText()
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

    try {
      $client = new Client(env('GEMINI_API_KEY'));
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
