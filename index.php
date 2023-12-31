<?php

require_once 'vendor/autoload.php';


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/vue');

$twig = new Environment($loader, [ 
    'debug' =>true,
    'cache' => false, // Mettez à jour en mode cache pour la production
]);


include 'C:\xampp\htdocs\livityshop\Controller.php';
$url = $_GET['url'] ?? 'accueil';
$session_id = session_id();

    switch ($url) {
        case 'accueil':
            getAccueil($twig);
            break;
        case 'connexion':
            connect($twig);
            break;
        case 'deconnexion':
            deconnexion();
            break;
        case 'inscription':
            getPageInscription($twig);
            break;
        case 'postInscription':
            inscription();
            break;
        case 'produits':
            productsPage($twig);
            break;

        case 'productDetails':
                if (isset($_GET['id'])) {
                    $productId = $_GET['id'];
                    productDetail($productId, $twig);
                }
                break;
            
        case 'ajouterPanier':
            ajouterObjetPanier();
            break;
        case 'panier':
            PanierPage();
            break;
        case 'supprimerPanier':
            supprimerPanier();
            break;
        case 'validerPanier':
            formValiderPanier();
            break;
        case 'validerOrder':
            validerOrder();
            break;
        case 'commande':
            afficherListeCommandes();
            break;
        case 'detailsCommande':
            afficherDetailCommande();
            break;
        case 'Entreprise':
            EntreprisePage($twig);
            break;


        default:
            getAccueil($twig);
            break;
    }
