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
        
        $entrepriseTemplate = $twig->load('entreprise.twig');

        
        echo $entrepriseTemplate->render();
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit; 
    }
}

function connect(\Twig\Environment $twig)
{
    try {
        
        $loginTemplate = $twig->load('logins.twig');

        
        echo $loginTemplate->render();
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit; 
    }
}

function getPageInscription(\Twig\Environment $twig)
{
    try {
        
        $inscripTemplate = $twig->load('inscription.twig');

        
        echo $inscripTemplate->render();
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit; 
    }
}










