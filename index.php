<?php

require_once 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/vue');

$twig = new Environment($loader, [
    'cache' => false, // Mettez à jour en mode cache pour la production
]);

// Fonction pour rendre le template en fonction de la route
function renderTemplate($twig, $route)
{
    switch ($route) {
        case '/':
            return $twig->render('homepage.twig', ['controller_name' => 'HomepageController']);
        case '/products':
            // Ajoutez la logique pour la page de produits
            return $twig->render('products.twig', ['controller_name' => 'ProductController']);
        case '/entreprise':
            // Ajoutez la logique pour la page d'entreprise
            return $twig->render('entreprise.twig', ['controller_name' => 'EntrepriseController']);
        default:
            // Page par défaut pour une route non reconnue
            return $twig->render('404.twig', ['controller_name' => 'NotFoundController']);
    }
}

// Récupération de la route depuis la requête
$route = isset($_GET['route']) ? $_GET['route'] : '/';

// Rendu du template en fonction de la route
$content = renderTemplate($twig, $route);

echo $content;
