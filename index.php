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
            getPageConnexion();
            break;
        case 'postConnection':
            connexion();
            break;
        case 'deconnexion':
            deconnexion();
            break;
        case 'inscription':
            getPageInscription();
            break;
        case 'postInscription':
            inscription();
            break;
        case 'produits':
            productsPage($twig);
            break;

        case 'produitsdetails':
            productDetail($productId, $twig);

        case 'ajouterPanier':
            ajouterObjetPanier();
            break;
        case 'panier':
            affichPanier();
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
