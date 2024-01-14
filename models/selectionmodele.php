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
    
            
            $sql = "SELECT * FROM orders WHERE session = ? ORDER BY date DESC LIMIT 1";
            $sth = $conn->prepare($sql);
            $sth->execute(array($session_id));
    
            // Utilisez la chaîne de classe complète pour fetchObject en spécifiant la classe Order
            $order = $sth->fetchObject(\App\Model\SelectionModele::class);
    
        
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
    
            $sql = "INSERT INTO orders(customer_id, registered, date, status, session) VALUE(?, 0, now(), 0, ?)";
            $sth = $conn->prepare($sql);
            $sth->execute(array($session_ID,$session_ID));
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
    
            $sql = "SELECT a.*, l.id AS user_id FROM admin a 
                    LEFT JOIN logins l ON a.username = l.username 
                    WHERE a.username=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($username));
    
            return $sth->fetchObject();
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }
    

    public function loginAdmin($username, $password) {
        $admin = $this->getUnAdmin($username);

        if ($admin && password_verify($password, $admin->password)) {
            return $admin;
        }

        return false;
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

    function getUserById($id) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM logins WHERE id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($id));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getCustomerInfo($idCustomer) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT *  FROM customers WHERE id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($idCustomer));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getLesAdresses($idCustomer) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM delivery_addresses d JOIN orders o ON d.id = o.delivery_add_id WHERE customer_id = ?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($idCustomer));
            return $sth->fetchAll();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function ajouterAdresse($prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "INSERT INTO delivery_addresses(firstname, lastname, add1, add2, city, postcode, phone, email) VALUE (?, ?, ?, ?, ?, ?, ?, ?)";
            $sth = $conn->prepare($sql);
            $sth->execute(array($prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getLastAdresse() {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM delivery_addresses ORDER BY id DESC";
            $sth = $conn->prepare($sql);
            $sth->execute(array());
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function updateCustomer($id, $prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "UPDATE customers SET forname=?, surname=?, add1=?, add2=?, add3=?, postcode=?, phone=?, email=? WHERE id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail, $id));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function createCustomer($prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "INSERT INTO customers(forname, surname, add1, add2, add3, postcode, phone, email, registered) VALUE( ?, ?, ?, ?, ?, ?, ?, ?, 1)";
            $sth = $conn->prepare($sql);
            $sth->execute(array( $prenom, $nom, $addr1, $addr2, $addr3, $cp, $tel, $mail));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function updateLastOrder($Id, $customerId, $idAdresse, $paiment, $statut) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "UPDATE orders SET customer_id = ?, status = ?, delivery_add_id = ?, payment_type = ? WHERE id = ?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($customerId, $statut, $idAdresse, $paiment, $Id));
          
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }


    function updateLastLogin($Id, $customerId) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "UPDATE logins SET customer_id = ? WHERE id = ?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($customerId, $Id));
          
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getUserByCustId($custId)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM logins WHERE customer_id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($custId));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    public function changervaleurregistered( $session)
    {
        try{
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "UPDATE orders SET registered=? WHERE customer_id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array(1, $session));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getLesCommandesAValider() {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM orders WHERE status=5";
            $sth = $conn->prepare($sql);
            $sth->execute(array());
            return $sth->fetchAll();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getOrderById($idOrder) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM orders WHERE id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($idOrder));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function getDeliveryAdressById($idAdresse) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM delivery_addresses WHERE id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($idAdresse));
            return $sth->fetchObject();
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }

    function changeStatusOrder($idOrder, $status) {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "UPDATE orders SET status=? WHERE id=?";
            $sth = $conn->prepare($sql);
            $sth->execute(array($status, $idOrder));
        }
        catch (PDOException $e)
        {
            $erreur = $e->getMessage();
            throw new \Exception($erreur);
        }
    }
}

