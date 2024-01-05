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
}


$userModel = new User();

// Example login attempt
$username = "john";
$password = "SHA1('John+123')";

try {
    $userData = $userModel->loginUser($username, $password);

    if ($userData) {
        // Login successful
        echo "Login successful! User ID: {$userData['customer_id']}, Username: {$userData['username']}";
    } else {
        // Login failed
        echo "Login failed. Invalid username or password.";
    }
} catch (\Exception $e) {
    // Handle exceptions
    echo "An error occurred: " . $e->getMessage();
}

?>