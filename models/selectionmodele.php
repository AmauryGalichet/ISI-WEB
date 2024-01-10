<?php

namespace App\Model;

use PDO;
use PDOException;
require('C:\xampp\htdocs\livityshop\models\connexion.php');

class SelectionModele
{
    public function getProduit($categoryId = null)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            // Use different queries based on whether a category is specified or not
            if ($categoryId !== null) {
                $sql = "SELECT * FROM products WHERE cat_id = ?";
                $sth = $conn->prepare($sql);
                $sth->execute([$categoryId]);
            } else {
                $sql = "SELECT * FROM products";
                $sth = $conn->prepare($sql);
                $sth->execute();
            }

            return $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de la récupération des produits : " . $erreur);
        }
    }


    public function getCategorie()
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM categories";
            $sth = $conn->prepare($sql);
            $sth->execute();

            return $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de la récupération des catégories : " . $erreur);
        }
    }

    public function getProductDetails($productId = null)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            // Use different queries based on whether a product ID is specified or not
            if ($productId !== null) {
                $sql = "SELECT * FROM products WHERE id = ?";
                $sth = $conn->prepare($sql);
                $sth->execute([$productId]);
            } else {
                throw new \InvalidArgumentException("Product ID is required for fetching product details.");
            }

            $productDetails = $sth->fetch(PDO::FETCH_ASSOC);


            return $productDetails;
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de la récupération des détails du produit : " . $erreur);
        }
    }

    function getLastOrderFromSession($session_id) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();
    
            // Ajoutez cet écho pour voir la valeur de $session_id avant la recherche de commande
            echo "Session ID before getLastOrderFromSession: " . $session_id . "\n";
    
            $sql = "SELECT * FROM orders WHERE session = ? ORDER BY date DESC LIMIT 1";
            $sth = $conn->prepare($sql);
            $sth->execute(array($session_id));
    
            // Utilisez la chaîne de classe complète pour fetchObject en spécifiant la classe Order
            $order = $sth->fetchObject(\App\Model\SelectionModele::class);
    
            // Ajoutez cet écho pour voir la valeur de $session_id après la recherche de commande
            echo "Session ID after getLastOrderFromSession: " . $session_id . "\n";
    
            return $order;
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }
    
    
    

    function getLastOrderFromID($customer_id) {
        try{
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM orders WHERE customer_id=? AND status=0";
            $sth = $conn->prepare($sql);
            $sth->execute(array($customer_id));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getProduitFromSessionOrder($session) {
        try{
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM orderitems JOIN orders o on orderitems.order_id = o.id WHERE session=? AND status=0";
            $sth = $conn->prepare($sql);
            $sth->execute(array($session));
            return $sth->fetchAll();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function createOrder($session_ID) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();
    
            $sql = "INSERT INTO orders(registered, date, status, session) VALUE(1, now(), 0, ?)";
            $sth = $conn->prepare($sql);
            $sth->execute(array($session_ID));
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }
    
    
    

    function isProduitExist($order_id, $product_id): bool
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM orderitems WHERE product_id = ? AND order_id = ?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($product_id, $order_id));
            $result = $sth->fetchAll();
            return !empty($result);
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getQuantityproductorder($order_id, $product_id) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT quantity FROM orderitems WHERE product_id = ? AND order_id = ?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($product_id, $order_id));
            $result = $sth->fetchObject();
            return $result->quantity;
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function updateOrderProduitQuantity($order_id, $product_id, $quantity) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "UPDATE orderitems SET quantity=? WHERE order_id=? AND product_id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($quantity, $order_id, $product_id));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }
    function getItemByOrderId($orderId) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT OD.id, p.cat_id, p.name, p.description, p.image, p.price, OD.quantity FROM orderitems OD JOIN products p on OD.product_id = p.id WHERE order_id = ?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($orderId));
            return $sth->fetchAll();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function addOrderItems($order_id, $product_id, $quantity) {
        try{
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "INSERT INTO orderitems(order_id, product_id, quantity) VALUE(?, ?, ?)";
            $sth = $conn->prepare($sql);
            $sth->execute(array($order_id, $product_id, $quantity));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    public function calculateTotal($OrderId)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT p.price, oi.quantity
                    FROM orderitems oi
                    JOIN products p ON oi.product_id = p.id
                    JOIN orders o ON oi.order_id = o.id
                    WHERE o.id = :OrderId";

            $sth = $conn->prepare($sql);
            $sth->bindParam(':OrderId', $OrderId, PDO::PARAM_INT);
            $sth->execute();

            $total = 0;

            while ($item = $sth->fetch(PDO::FETCH_ASSOC)) {
                $total += $item['quantity'] * $item['price'];
            }

            $sql = "UPDATE orders set total = :total WHERE id = :OrderId";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':total', $total, PDO::PARAM_INT);
            $sth->bindParam(':OrderId', $OrderId, PDO::PARAM_INT);

            $sth->execute();

            return $total;

        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors du calcul du total du panier : " . $erreur);
        }
    }

    function delOrderItemRow($id) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "DELETE FROM orderitems WHERE id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($id));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getUnAdmin($username) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM admin WHERE username=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($username));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function addUser($username, $password) {
        try{
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "INSERT INTO logins(username, password) VALUE (?, ?)";
            $sth = $conn->prepare($sql);
            $sth->execute(array($username, password_hash($password, PASSWORD_DEFAULT)));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getUserByUsername($username) {
        try{
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM logins WHERE username=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($username));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function addCustomerIdToOrder($customerID, $sessionID) {
        try{
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "UPDATE orders SET customer_id=? WHERE session=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($customerID, $sessionID));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getItemQuantityInOrderItem($idOrder, $idProducts) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT quantity from orderitems WHERE product_id=? AND order_id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($idProducts, $idOrder));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function supprimerPanierSession($session) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "DELETE FROM orders WHERE session=? AND customer_id IS NULL";
            $sth = $conn->prepare($sql);
            $sth->execute(array($session));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }
}

