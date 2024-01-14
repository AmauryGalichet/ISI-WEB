<?php


use Twig\Environment;

require_once 'vendor/autoload.php';
require 'models/selectionmodele.php';
require 'invoice.php';

//nous avions un warning dont nous ne connaissions pas la source 
// afin d'eviter l'affichage de ce dernier , nous avons preferer utiliser cette fonction
error_reporting(E_ERROR | E_PARSE);


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
            $dbx->createOrder($customer_id);
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
    global $twig;

    // Vérifiez d'abord si l'administrateur est déjà connecté
    $isAdminConnected = isset($_SESSION['idAdmin']);

    // Si l'administrateur est connecté, redirigez vers la page appropriée
    if ($isAdminConnected) {
        try {
            echo $twig->render('isconnected.twig', ['isAdminConnected' => $isAdminConnected]);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
        return;
    }

    // Si le formulaire est soumis
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $dbx = new \App\Model\SelectionModele();
        
        // Ajoutez des messages de débogage
        echo "Username: $username, Password: $password";

        // Vérifiez d'abord si l'utilisateur existe dans la table logins
        $existingUser = $dbx->getUserByUsername($username);

        if (!empty($existingUser) && password_verify($password, $existingUser->password)) {
            // L'utilisateur existe, connectez-le
            $_SESSION['username'] = $username;

            if (!empty($existingUser->user_id)) {
                // Utilisateur trouvé dans la table logins (logins)
                $_SESSION['id'] = $existingUser->user_id;
                fusionnerPanierSiPossible($existingUser->user_id);
                $templateName = 'isconnected.twig';
            } else {
                // Utilisateur trouvé seulement dans la table admin
                $_SESSION['idAdmin'] = $existingUser->id;
                $templateName = 'isconnected.twig';
            }

            try {
                // Ajoutez des messages de débogage
                echo "User connected successfully!";
                echo $twig->render('base.twig', ['isAdminConnected' => $isAdminConnected]);
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        } else {
            // Ajoutez des messages de débogage
            echo "Invalid username or password";
            $templateName = 'notconnected.twig';

            try {
                echo $twig->render('base.twig', ['isAdminConnected' => $isAdminConnected]);
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }
    } else {
        // Si le formulaire n'est pas soumis, redirigez vers la page appropriée
        try {
            echo $twig->render('base.twig', ['isAdminConnected' => $isAdminConnected]);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            exit;
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
        $dbx->changervaleurregistered($session);
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
        $dbx->changervaleurregistered($session);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

}

function deconnexion() {
    session_destroy();
    $_SESSION = array();
    render('homepage.twig');
}

function formValiderPanier1() {
    global $twig;
    try {
        if (!isset($_SESSION['id'])) {
            connect($twig);
        } else {
            $dbx = new \App\Model\SelectionModele();
            $userInfo = $dbx->getUserById($_SESSION['id']);
            $customerInfo = $dbx->getCustomerInfo($userInfo->customer_id);
            $unOrder = getOrder($_SESSION['id']);
            $total = $dbx->calculateTotal($unOrder->id);
            $lesArticles = $dbx->getItemByOrderId($unOrder->id);
            $lesAdresses = $dbx->getLesAdresses($userInfo->customer_id);

            try {
                $productsTemplate = $twig->load('Etape1.twig');
                echo $productsTemplate->render([
                    'var1' => $lesArticles,
                    'var2' => $userInfo,
                    'var3' => $customerInfo,
                    'var4' => $lesAdresses,
                    'total' => $total

                ]);
            }
            catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }
    }
    catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

function validerOrder()
{
    global $twig;
    try {
        $idCust = $_SESSION['id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $addr1 = $_POST['addr1'];
        $addr2 = $_POST['addr2'];
        $addr3 = $_POST['addr3'];
        $cp = $_POST['cp'];
        $tel = $_POST['tel'];
        $mail = $_POST['mail'];
        $paiment = $_POST['paiment'];
        $dbx = new \App\Model\SelectionModele();
        $custInfo = $dbx->getCustomerInfo($idCust);
        $login = $dbx->getUserById($_SESSION['id']);
        $Order = getOrder($_SESSION['id']);
        $dbx->ajouterAdresse($prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail);
        $adresse = $dbx->getLastAdresse();
        if ($custInfo) {
            $dbx->updateCustomer($idCust, $prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail);
        } else {
            $dbx->createCustomer( $prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail);
        }
        $result = $dbx->getCustomerInfo($idCust);
        print_r($result); // Ajoutez cette ligne pour débogage
        $IdL = $login->id;
        $IdO = $Order->id;
        $Custid= $custInfo->id;
        $dbx->updateLastLogin($IdL, $Custid);
        echo "updateLastLogin a été exécuté"; // Ajoutez cette ligne pour débogage
        $connecte = $dbx->getUserByCustId($idCust);
        if($connecte != NULL)
        {
            $dbx->changervaleurregistered($result->id);
        }
        $dbx->updateLastOrder($IdO, $idCust, $adresse->id, $paiment, "5");
        echo "updateLastOrder a été exécuté"; // Ajoutez cette ligne pour débogage
        if($paiment == "cheque"){
            try {
                $inscripTemplate = $twig->load('ValiderCheque.twig');
                echo $inscripTemplate->render();
            }
            catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }}
        else{
            try {
                $inscripTemplate = $twig->load('ValiderPaypal.twig');
                   echo $inscripTemplate->render();
            }
            catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
                }
        }

    } catch (Exception $e) {
        echo "Error:". $e->getMessage();
    }
}

function createfacture()
{
    // (c) Xavier Nicolay
    // Exemple de génération de devis/facture PDF
 // Assurez-vous de mettre le bon chemin vers votre fichier PDF
    $dbx = new \App\Model\SelectionModele();
    $idCust = $_SESSION['id'];
    $dateActuelle = date("Y-m-d");
    $custInfo = $dbx->getCustomerInfo($idCust);
    $custforname = utf8_decode($custInfo->forname);
    $custsurname = utf8_decode($custInfo->surname);
    $custadd = utf8_decode($custInfo->add1);
    $custcp = utf8_decode($custInfo->postcode);
    $Order = getOrder($_SESSION['id']); // Assurez-vous de remplacer cela par votre fonction réelle
    $OrderId = $Order->id;
    $products = $dbx->getItemByOrderId($OrderId);
    $paiment = utf8_decode($Order->payment_type);
    ob_start();
    ob_clean();
    $pdf = new PDF_Invoice('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->addSociete("LIVITYSHOP",
        utf8_decode("15 Bd André Latarjet\n") .
        utf8_decode("69100 Villeurbanne\n") .
        "R.C.S. PARIS B 000 000 007\n" .
        "Capital : ____" . EURO);
    $pdf->temporaire("Devis temporaire");
    $pdf->addDate("$dateActuelle");
    $pdf->addClient("$custforname $custsurname");
    $pdf->addPageNumber("1");
    $pdf->addClientAdresse("$custadd\n$custcp");
    $pdf->addReglement("$paiment");
    $pdf->addEcheance("$dateActuelle");
    $pdf->addNumTVA("FR888777666");
    $cols = array("NOMPRODUIT" => 29,
        "DESCRIPTION" => 100,
        "QUANTITE" => 20,
        "MONTANT" => 41);
    $pdf->addCols($cols);
    $cols = array("NOMPRODUIT" => "L",
        "DESCRIPTION" => "L",
        "QUANTITE" => "L",
        "MONTANT" => "R"); // Modification de la position de la colonne MONTANT
    $pdf->addLineFormat($cols);
    $pdf->addLineFormat($cols);
    $y = 109;
    foreach ($products as $product) {
        $line = array(
            "NOMPRODUIT" => $product["name"],
            "DESCRIPTION" => $product["description"],
            "QUANTITE" => $product["quantity"],
            "MONTANT" => $product["price"],
        );
        $size = $pdf->addLine($y, $line);
        $y += $size + 2;
    }

    $pdf->Output();
}

function afficherListeCommandes() {
    global $twig;
    try {
        $dbx = new \App\Model\SelectionModele();
        $lesCommandes = $dbx->getLesCommandesAValider();
        try {
            $productsTemplate = $twig->load('InterfaceAdmin.twig');
            echo $productsTemplate->render(['var1' => $lesCommandes]);
        }
        catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
        render('InterfaceAdmin.twig', null, $lesCommandes);
    }
    catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

function afficherDetailCommande() {
    global $twig;
    try {
        $dbx = new \App\Model\SelectionModele();
        $idOrder = $_GET['id'];
        $lesProduits = $dbx->getItemByOrderId($idOrder);
        $unOrder = $dbx->getOrderById($idOrder);
        $adresse = $dbx->getDeliveryAdressById($unOrder->delivery_add_id);
        try {
            $productsTemplate = $twig->load('detailsCommande.twig');
            echo $productsTemplate->render(['var1' => $lesProduits, 'var2' => $unOrder, 'var3' => $adresse]);
        }
        catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
    catch (Exception $e) {
        echo "Error: " . $e->getMessage();
                exit;
    }
}

function validerEnvoi() {
    try {
        $dbx = new \App\Model\SelectionModele();
        $idOrder = $_GET['idOrder'];
        $dbx->changeStatusOrder($idOrder, 10);
        afficherListeCommandes();
    }
    catch (Exception $e) {
        echo "Error: " . $e->getMessage();
                exit;
    }
}