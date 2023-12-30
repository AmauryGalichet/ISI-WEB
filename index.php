<?php

require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;

(new Dotenv())->bootEnv(__DIR__.'vendor/.env');

require __DIR__.'/config/bootstrap.php';

$kernel = new HttpKernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);

// Autres manipulations pour Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/src/templates');
$twig = new \Twig\Environment($loader);

// Rendre le modèle Twig
$template = $twig->load('cart.html.twig');
$content = $template->render();

$response->setContent($content);  // Remplacez le contenu de la réponse avec le rendu Twig

// Envoyez la réponse au navigateur
$response->send();

// Terminez la demande
$kernel->terminate($request, $response);
