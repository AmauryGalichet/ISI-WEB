<?php


/* récupérer le tableau des données */
require 'models/selectionmodele.php';
$selectionModele = new \App\Model\SelectionModele();

/* Récupérer tous les produits */
try {
    $allProducts = $selectionModele->getProduit();
    var_dump($allProducts);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
    exit; // En cas d'erreur, arrêter l'exécution
}

/* inclure l'autoloader */
require_once 'vendor/autoload.php';

/* templates chargés à partir du système de fichiers (répertoire vue) */
$loader = new Twig\Loader\FilesystemLoader('vue');

/* options : prod = cache dans le répertoire cache, dev = pas de cache */
$options_prod = array('cache' => 'cache', 'autoescape' => true);
$options_dev = array('cache' => false, 'autoescape' => true);

/* stocker la configuration */
$twig = new Twig\Environment($loader);

/* charger+compiler le template, exécuter, envoyer le résultat au navigateur */
echo $twig->render('products.twig', ['products' => $allProducts]);
?>
