<?php

namespace App\Model;

use PDO;
use PDOException;

require('C:\xampp\htdocs\livityshop\models\connexion.php');

class CartModele
{
    public function addToCart($userId, $productId, $quantity)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();
            
            // Insert or update the quantity if the product is already in the cart
            $sql = "INSERT INTO carts (user_id, product_id, quantity)
                    VALUES (:userId, :productId, :quantity)
                    ON DUPLICATE KEY UPDATE quantity = quantity + :quantity";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':userId', $userId, PDO::PARAM_INT);
            $sth->bindParam(':productId', $productId, PDO::PARAM_INT);
            $sth->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $sth->execute();
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de l'ajout au panier : " . $erreur);
        }
    }

    public function removeFromCart($userId, $productId)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();
            
            $sql = "DELETE FROM carts WHERE user_id = :userId AND product_id = :productId";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':userId', $userId, PDO::PARAM_INT);
            $sth->bindParam(':productId', $productId, PDO::PARAM_INT);
            $sth->execute();
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de la suppression du panier : " . $erreur);
        }
    }

    public function getCartProducts($userId)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();
            
            $sql = "SELECT p.id, p.name, p.description, p.image, p.price, oi.quantity
            FROM orderitems oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            JOIN logins l ON o.customer_id = l.customer_id
            WHERE l.id = :userId;";
        
            $sth = $conn->prepare($sql);
            $sth->bindParam(':userId', $userId, PDO::PARAM_INT);
            $sth->execute();

            return $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de la récupération du panier : " . $erreur);
        }
    }
    public function calculateTotal($userId)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();
            
            $sql = "SELECT p.price, oi.quantity
                    FROM orderitems oi
                    JOIN products p ON oi.product_id = p.id
                    JOIN orders o ON oi.order_id = o.id
                    WHERE o.id = :userId";

            $sth = $conn->prepare($sql);
            $sth->bindParam(':userId', $userId, PDO::PARAM_INT);
            $sth->execute();

            $total = 0;

            while ($item = $sth->fetch(PDO::FETCH_ASSOC)) {
                $total += $item['quantity'] * $item['price'];
            }

            return $total;
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors du calcul du total du panier : " . $erreur);
        }
    }
}


$cartModele = new \App\Model\CartModele();

// Assuming user ID is 1
$userId = 1;


try {
    $cartProducts = $cartModele->getCartProducts($userId);

    if (!empty($cartProducts)) {
        foreach ($cartProducts as $product) {
            echo "Product ID: {$product['id']}, Name: {$product['name']}, Quantity: {$product['quantity']}, Total Price: {$product['price']} <br>";
        }
    } else {
        echo "The cart is empty.";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

$userId = 1; // Replace with the actual user ID
$cartModele = new \App\Model\CartModele();
$total = $cartModele->calculateTotal($userId);
echo $total;

// Now $total contains the calculated total amount