<?php

require 'c:\xampp\htdocs\livityshop\models\selectionmodele.php';



$selectionModele = new \App\Model\SelectionModele();

// Récupérer tous les produits
try {
    $allProducts = $selectionModele->getProduit();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
    exit; // En cas d'erreur, arrêter l'exécution
}

// Récupérer toutes les catégories
try {
    $allCategories = $selectionModele->getCategorie();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
    exit; 
}


require_once 'c:\xampp\htdocs\livityshop\vendor\autoload.php';


$loader = new Twig\Loader\FilesystemLoader('c:\xampp\htdocs\livityshop\vue\products');


$options = [
    'cache' => 'cache',
    'autoescape' => true
];


$twig = new Twig\Environment($loader, $options);


echo $twig->render('productsde.twig', ['products' => $allProducts, 'categories' => $allCategories]);
?>
