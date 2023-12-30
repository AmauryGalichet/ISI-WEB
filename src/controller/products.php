<?php
// src/Controller/ProductsController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\SelectionModele;

class ProductsController extends AbstractController
{
    
    public function index(): Response
    {
        $modele = new SelectionModele();
        $products = $modele->getProduit(1);

        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }
}


// products.php

require_once 'vendor/autoload.php';

// Instanciation de la classe `SelectionModele`
$selectionModele = new SelectionModele();

// Créez l'environnement Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

try {
    // Exemple: Get all products
    $allProducts = $selectionModele->getProduit();

    // Charge le template Twig
    $template = $twig->load('products.twig');

    // Rend le template avec les données
    echo $template->render(['products' => $allProducts]);

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

