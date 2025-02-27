<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/frame', 'Home::index');
$routes->get('/', 'AIController::index');
$routes->post('/ai-quester', 'AIController::generateAIText');

$routes->post('/gemini-answer', 'AIController::geminiAIText');

$routes->post('generate-text', 'OpenAIController::generateText');

$routes->group('gemini-agent', function ($routes) {
  $routes->post('response-text', 'ClientAgent\GeminiAgent::responseText');
});

$routes->group('universal-agent', function ($routes) {
  $routes->post('response-text', 'ClientAgent\UniversalAgent::responseText');
});
