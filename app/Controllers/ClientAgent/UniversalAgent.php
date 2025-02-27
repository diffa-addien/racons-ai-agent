<?php

namespace App\Controllers\ClientAgent;

use App\Controllers\BaseController;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class UniversalAgent extends BaseController
{
  public function index(): object
  {
    return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
  }

  public function responseText()
  {
    // Ambil data JSON dari request body
    $requestBody = json_decode($this->request->getBody(), true);
    $text = $requestBody['message'] ?? ''; // Ambil 'message' dari JSON, default kosong jika tidak ada

    if (empty($text)) {
      return $this->response->setJSON([
        'error' => true,
        'message' => 'Field "message" tidak boleh kosong'
      ])->setStatusCode(400);
    }

    // Inisialisasi Guzzle Client
    $client = new Client([
      'base_uri' => 'https://api.openai.com/v1/',
      'timeout'  => 10.0,
    ]);

    try {
      // Kirim request POST ke API OpenAI
      $response = $client->post('chat/completions', [
        'headers' => [
          'Authorization' => 'Bearer API_KEY', // Ganti dengan API Key Anda
          'Content-Type'  => 'application/json',
        ],
        'json' => [
          'model' => 'gpt-4o',
          'store' => true,
          'messages' => [
            [
              'role' => 'user',
              'content' => $text // Gunakan input 'message' dari user
            ]
          ]
        ],
      ]);

      // Decode hasil dari API
      $result = json_decode($response->getBody()->getContents(), true);

      // Kembalikan hasil langsung sebagai JSON
      return $this->response->setJSON($result);
    } catch (RequestException $e) {
      // Kembalikan error sebagai JSON
      return $this->response->setJSON([
        'error' => true,
        'message' => $e->getMessage()
      ])->setStatusCode(500);
    }
  }
}
