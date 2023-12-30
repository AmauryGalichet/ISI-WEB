<?php

namespace App\Model;

use PDO;
use PDOException;
require('C:\xampp\htdocs\livityshop\src\models\database\connexion.php');

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

            $products = $sth->fetchAll(PDO::FETCH_ASSOC);

            return $products;
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
}

$selectionModele = new \App\Model\SelectionModele();



// Example 2: Get all categories
try {
    $categories = $selectionModele->getCategorie();
    
    // Display or process the retrieved categories
    foreach ($categories as $category) {
        echo "Category ID: " . $category['id'] . ", Name: " . $category['name'] . "<br>";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

$selectionModele = new \App\Model\SelectionModele();

try {
    // Example: Get all products
    $allProducts = $selectionModele->getProduit();
    
    // Display or process the retrieved products
    foreach ($allProducts as $product) {
        echo "Product ID: " . $product['id'] . "<br>";
        echo "Category ID: " . $product['cat_id'] . "<br>";
        echo "Name: " . $product['name'] . "<br>";
        echo "Description: " . $product['description'] . "<br>";
        echo "Image: " . $product['image'] . "<br>";
        echo "Price: " . $product['price'] . "<br>";
        echo "<br>";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>