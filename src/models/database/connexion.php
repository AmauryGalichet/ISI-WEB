<?php
namespace App\Model;

use PDO;
use PDOException;

class Setup
{
    private $cnx = null;
    private $dbhost;
    private $dbbase;
    private $dbuser;
    private $dbpwd;

    public function __construct()
    {
        $this->dbhost = 'localhost';
        $this->dbbase = 'web4shop';
        $this->dbuser = 'root';
        $this->dbpwd = 'root';
    }

    public function getConnexion()
    {
        try {
            $this->cnx = new PDO("mysql:host={$this->dbhost};dbname={$this->dbbase}", $this->dbuser, $this->dbpwd);
            $this->cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->cnx->exec('SET CHARACTER SET utf8');

        } catch (PDOException $e) {
            $erreur = $e->getMessage();
        }

        return $this->cnx;
    }

    public function isConnected()
    {
        return $this->cnx !== null;
    }
}

// Example of usage
$setup = new Setup();
$connection = $setup->getConnexion();

if ($setup->isConnected()) {
    echo "Connected to the database!";
} else {
    echo "Failed to connect to the database.";
}
?>
