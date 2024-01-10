<?php


use Twig\Environment;

require_once 'vendor/autoload.php';
require 'models/selectionmodele.php';


$loader = new \Twig\Loader\FilesystemLoader('C:\xampp\htdocs\livityshop\vue');
$twig = new Environment($loader);

function getAccueil($twig) {
    
    echo $twig->render('homepage.twig');
}

function render($page, $message=null, $var1=null, $var2=null, $var3=null, $var4=null) {
    require ('vue/base.twig');
}


function productDetail($productId, $twig)
{
    $selectionModele = new \App\Model\SelectionModele();

    try {
        $productDetails = $selectionModele->getProductDetails($productId);

        if ($productDetails === null) {
            echo $twig->render('productsde.twig', ['error' => 'Product details not found']);
            return;
        }

        echo $twig->render('productsde.twig', [
            'productDetails' => $productDetails,
        ]);
    } catch (\Exception $e) {
        echo $twig->render('productde.twig', ['error' => 'An error occurred while fetching product details']);
    }
}




function productsPage(Environment $twig)
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


function EntreprisePage(Environment $twig)
{
    try {
        
        $entrepriseTemplate = $twig->load('entreprise.twig');

        
        echo $entrepriseTemplate->render();
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit; 
    }
}

function connect(Environment $twig)
{
    try {
        
        $loginTemplate = $twig->load('logins.twig');

        
        echo $loginTemplate->render();
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit; 
    }
}

function getPageInscription(Environment $twig)
{
    try {
        
        $inscripTemplate = $twig->load('inscription.twig');

        
        echo $inscripTemplate->render();
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit; 
    }
}
function getOrderIdFromSession($session_id) {
    try {
        $dbx = new \App\Model\SelectionModele();
        
        // Utiliser la fonction getLastOrderFromSession pour obtenir la dernière commande
        $order = $dbx->getLastOrderFromSession($session_id);

        // Vérifier si la commande existe avant d'accéder à sa propriété 'id'
        if ($order && isset($order->id)) {
            // Retourner l'identifiant de la commande
            return $order->id;
        } else {
            // Si la commande n'existe pas, créer une nouvelle commande
            $dbx->createOrder($session_id);

            // Refetch la commande après sa création
            $newOrder = $dbx->getLastOrderFromSession($session_id);

            // Vérifier à nouveau si la commande existe avant de retourner l'identifiant
            if ($newOrder && isset($newOrder->id)) {
                return $newOrder->id;
            } else {
                // Gérer le cas où la commande n'est toujours pas trouvée
                return null;
            }
        }
    } catch (Exception $e) {
        // Gérer les exceptions
        echo "Error: " . $e->getMessage();
        return null;
    }
}





function getOrder($customer_id = null) {
    try {
        $dbx = new \App\Model\SelectionModele();

        // Si pas connecté
        if ($customer_id == null) {
            if (!isset($_SESSION['id'])) {
                session_start();
                $_SESSION['id'] = uniqid(); // Générez un identifiant de session unique
            }
            $customer_id = $_SESSION['id'];
        }

        // Vérifier si la commande existe avec le customer_id associé à la session
        $unOrder = $dbx->getLastOrderFromSession($customer_id);

        // Si la commande n'existe pas, créez une nouvelle commande
        if (empty($unOrder)) {
            $dbx->createOrder(session_id());
            // Refetch la commande après sa création
            $unOrder = $dbx->getLastOrderFromSession($customer_id);
        }

        return $unOrder;
    } catch (Exception $e) {
        // Gérer les exceptions
        echo "Error: " . $e->getMessage();
        return null; // Retourner null en cas d'erreur
    }
}


function affichPanier($twig) {
    try {
        $dbx = new \App\Model\SelectionModele();

        if (isset($_SESSION['id'])) {
            $orderId = getOrderIdFromSession($_SESSION['id']);
        } else {
            $orderId = getOrderIdFromSession();
        }

        // Vérifier si $orderId est valide
        if ($orderId !== null) {
            $total = $dbx->calculateTotal($orderId);
            $lesArticles = $dbx->getItemByOrderId($orderId);
            $productsTemplate = $twig->load('cart.twig');

            echo $productsTemplate->render([
                'products' => $lesArticles,
                'total' => $total,
            ]);
        } else {
            // Gérer le cas où $orderId n'est pas valide
            echo "Error: Order not found.";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

function ajouterObjetPanier() {
    global $twig;

    try {
        // Si connecté
        if (isset($_SESSION['id'])) {
            $unOrder = getOrder($_SESSION['id']);
        } else {
            $unOrder = getOrder();
        }

        // Récupérer l'ID de la commande associée au customerid de la session
        $orderId = getOrderIdFromSession($_SESSION['id']);

        // Utilisez $orderId lors de l'insertion dans orderitems
        $idProduit = $_GET['idProduit'];
        $quantity = $_GET['quantity'];

        // On insère ensuite dans orderitem
        $dbx = new \App\Model\SelectionModele();

        // On vérifie que si le produit existe déjà
        if ($dbx->isProduitExist($orderId, $idProduit)) {
            $quantiteInitiale = $dbx->getQuantityproductorder($orderId, $idProduit);
            $dbx->updateOrderProduitQuantity($orderId, $idProduit, $quantiteInitiale + $quantity);
        } else {
            // Utilisez $orderId lors de l'insertion dans orderitems
            $dbx->addOrderItems($orderId, $idProduit, $quantity);
        }

        affichPanier($twig);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


function supprimerPanier() {
    global $twig;
    try {
        $idOrderItem = $_GET['idProduit'];
        $dbx = new \App\Model\SelectionModele();
        $dbx->delOrderItemRow($idOrderItem);
        affichPanier($twig);
    }catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

function connexion() {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username=$_POST['username'];
        $password=$_POST['password'];
        $dbx = new \App\Model\SelectionModele();
        $unAdmin = $dbx->getUnAdmin($username);
        if ($unAdmin != false) {
            if (password_verify($password, $unAdmin->password)) {
                $_SESSION['username'] = $username;
                $_SESSION['idAdmin'] = $unAdmin->id;
                return render('entreprise.twig');

            }
        }
        $unUserDB=$dbx->getUserByUsername($username);
        if (!empty($unUserDB)) {
            if (password_verify($password, $unUserDB->password)) {
                $_SESSION['username'] = $username;
                $_SESSION['id'] = $unUserDB->id;
                fusionnerPanierSiPossible($unUserDB->id);
                render('inscription.twig');
            } else {
                render('logins.twig', 'Mot de passe incorrect');
            }
        } else {
            render('logins.twig', 'Utilisateur incorrect');
        }
    }
}

function inscription() {
    global $twig;
    if (isset($_POST['username'], $_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            $dbx = new \App\Model\SelectionModele();
            $dbx->addUser($username, $password);
            getAccueil($twig);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Gérer le cas où les valeurs ne sont pas envoyées dans $_POST
        render('inscription.twig', 'Veuillez fournir un nom d\'utilisateur et un mot de passe');
    }
}

function fusionnerPanierSiPossible($idCustomer) {
    try {
        $dbx = new \App\Model\SelectionModele();
        $session = session_id();
        $panierSession = $dbx->getLastOrderFromSession($session);
        //Si il y a un panier session
        if ($panierSession != false) {
            $panierID = $dbx->getLastOrderFromID($idCustomer);
            //Si il n'y a pas de panier utilisateur alors on convertit le panier session en utilisateur
            if ($panierID == false) {
                $dbx->addCustomerIdToOrder($idCustomer, $session);
                //Si il y a un panier utilisateur
            } else {
                $lesProduitsSession = $dbx->getProduitFromSessionOrder($session);
                //Pour chaque produit du panier session
                foreach ($lesProduitsSession as $unProduit) {
                    $panierIdId = $panierID->id;
                    $produitId = $unProduit['product_id'];
                    $quantiteUnProdID = $dbx->getItemQuantityInOrderItem($panierIdId, $produitId);
                    //Si le produit n'existe pas dans le panier utilisateur alors on l'ajoute dedans
                    if ($quantiteUnProdID == false) {
                        $dbx->addOrderItems($panierIdId, $produitId, $unProduit['quantity']);
                        //Sinon, il existe et on met a jour dans le panier utilisateur
                    } else {
                        $quantiteFinale = $quantiteUnProdID->quantity+$unProduit['quantity'];
                        $dbx->updateOrderProduitQuantity($panierIdId, $produitId, $quantiteFinale);
                    }
                }
                $dbx->supprimerPanierSession($session);
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

}

function deconnexion() {
    session_destroy();
    $_SESSION = array();
    render('homepage.twig');
}