<?php

require_once 'vendor/autoload.php';
require 'models/selectionmodele.php';

$loader = new \Twig\Loader\FilesystemLoader('C:\xampp\htdocs\livityshop\vue');
$twig = new \Twig\Environment($loader);

function getAccueil($twig) {
    
    echo $twig->render('homepage.twig');
}

function productDetail($productId, $twig)
{
    
    $selectionModele = new \App\Model\SelectionModele();

    try {
        $productDetails = $selectionModele->getProductDetails($productId);
    } catch (\Exception $e) {
       
        echo $twig->render('productsde.twig', ['error' => 'Product details not found']);
        return;
    }

    echo $twig->render('product_detail.twig', [
        'productDetails' => $productDetails,
        
    ]);
}





function productsPage(\Twig\Environment $twig)
{
    try {
        $selectionModele = new \App\Model\SelectionModele();

        // Récupérer tous les produits depuis le modèle
        $allProducts = $selectionModele->getProduit();

        $productsTemplate = $twig->load('products.twig');

        echo $productsTemplate->render(['products' => $allProducts]);
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit; 
    }
}


function EntreprisePage(\Twig\Environment $twig)
{
    try {
        // Charger le template entreprise.twig
        $entrepriseTemplate = $twig->load('entreprise.twig');

        // Rendre le template de l'entreprise
        echo $entrepriseTemplate->render();
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit; // En cas d'erreur, arrêter l'exécution
    }
}








