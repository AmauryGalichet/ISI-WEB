<?php

namespace App\Model;

use PDO;
use PDOException;
require('C:\xampp\htdocs\livityshop\models\connexion.php');

class User
{
    public function loginUser($username, $password)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM logins WHERE username = :username AND password = :password";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':password', $password, PDO::PARAM_STR);
            $sth->execute();

            return $sth->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de la connexion de l'utilisateur : " . $erreur);
        }
    }

    public function registerUser($forname, $surname, $add1, $add2, $add3, $postcode, $phone, $email, $registered)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "INSERT INTO customers (forname, surname, add1, add2, add3, postcode, phone, email, registered) 
                    VALUES (:forname, :surname, :add1, :add2, :add3, :postcode, :phone, :email, :registered)";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':forname', $forname, PDO::PARAM_STR);
            $sth->bindParam(':surname', $surname, PDO::PARAM_STR);
            $sth->bindParam(':add1', $add1, PDO::PARAM_STR);
            $sth->bindParam(':add2', $add2, PDO::PARAM_STR);
            $sth->bindParam(':add3', $add3, PDO::PARAM_STR);
            $sth->bindParam(':postcode', $postcode, PDO::PARAM_STR);
            $sth->bindParam(':phone', $phone, PDO::PARAM_STR);
            $sth->bindParam(':email', $email, PDO::PARAM_STR);
            $sth->bindParam(':registered', $registered, PDO::PARAM_INT);

            $sth->execute();
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de l'inscription de l'utilisateur : " . $erreur);
        }
    }

    public function isUserRegistered($email)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT COUNT(*) FROM customers WHERE email = :email";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':email', $email, PDO::PARAM_STR);
            $sth->execute();

            return (bool) $sth->fetchColumn();
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de la vérification de l'inscription de l'utilisateur : " . $erreur);
        }
    }

    public function loginAdmin($username, $password)
    {
        try {
            $setup = new Setup();
            $conn = $setup->getConnexion();

            $sql = "SELECT * FROM admin WHERE username = :username AND password = :password";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':password', $password, PDO::PARAM_STR);
            $sth->execute();

            return $sth->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $erreur = $e->getMessage();
            throw new \Exception("Erreur lors de la connexion de l'administrateur : " . $erreur);
        }
    }
    
    // Fonction pour obtenir la liste des commandes
    public function getOrdersList($conn)
    {
        try {
            // Requête SQL pour obtenir la liste des commandes avec les informations associées
            $sql = "SELECT orders.id, customers.forname, customers.surname, 
                           delivery_addresses.add1 AS delivery_address, orders.status, 
                           orders.total, orders.date
                    FROM orders
                    JOIN customers ON orders.customer_id = customers.id
                    LEFT JOIN delivery_addresses ON orders.delivery_add_id = delivery_addresses.id";

            $sth = $conn->prepare($sql);
            $sth->execute();

            // Récupération des résultats
            $ordersList = $sth->fetchAll(PDO::FETCH_ASSOC);

            return $ordersList;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des commandes : " . $e->getMessage());
        }
    }
    
    
    
    // Fonction pour obtenir le statut de la commande en texte
    function getOrderStatusText($statusCode) {
        switch ($statusCode) {
            case 0:
                return "L'utilisateur ajoute toujours des articles à son panier.";
            case 1:
                return "L'utilisateur a entré son adresse.";
            case 2:
                return "L'utilisateur a payé pour l'article.";
            case 10:
                return "L'administrateur a confirmé la transaction et envoyé l'élément.";
            default:
                return "Statut inconnu";
        }
    }
    
    
}

