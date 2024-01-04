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

    public function getProductDetails($productId)
{
    try {
        $setup = new Setup();
        $conn = $setup->getConnexion();

        $sql = "SELECT * FROM products WHERE id = ?";
        $sth = $conn->prepare($sql);
        $sth->execute([$productId]);

        $productDetails = $sth->fetch(PDO::FETCH_ASSOC);

        return $productDetails;
    } catch (PDOException $e) {
        $erreur = $e->getMessage();
        throw new \Exception("Error fetching product details: " . $erreur);
    }
}

}




