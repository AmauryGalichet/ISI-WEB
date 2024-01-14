<?php

require_once 'vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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

$isAdminConnected = isset($_SESSION['idAdmin']);
    switch ($url) {
        case 'accueil':
            getAccueil($twig);
            break;
        case 'connexion':
            connect($twig);
            break;
        case 'postConnection':
            connexion();
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
            affichPanier($twig);
            break;
        case 'supprimerPanier':
            supprimerPanier();
            break;
        case 'Entreprise':
            EntreprisePage($twig);
            break;
        case 'ValiderPanier':
            formValiderPanier1();
            break;
        case 'ValiderPanier1':
            ValiderPanier();
            break;
        case 'validerOrder':
            validerOrder();
            break;
        case 'FaireFacture':
            createfacture();
            break;
        case 'commande':
                // Vérifiez si l'administrateur est connecté
                if ($isAdminConnected) {
                    afficherListeCommandes();
                } else {
                    // Redirigez l'utilisateur vers une page appropriée en cas d'accès non autorisé
                    // Vous pouvez personnaliser cette redirection selon vos besoins
                    header('Location: ?action=notauthorized');
                    exit;
                }
                break;
        case 'detailsCommande':
                    // Vérifiez si l'administrateur est connecté
                    if ($isAdminConnected) {
                        afficherDetailCommande();
                    } else {
                        
                        
                        header('Location: ?action=notauthorized');
                        exit;
                    }
                    break;
        case 'validerEnvoi':
            validerEnvoi();
            break;


        default:
            getAccueil($twig);
            break;
    }