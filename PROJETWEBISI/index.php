<?php
$directory = __DIR__;
$files = scandir($directory);

echo "<h2>Contenu du répertoire : $directory</h2>";
echo "<ul>";

foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        $path = $directory . DIRECTORY_SEPARATOR . $file;
        $url = 'http://localhost/livityshop/' . $file;

        echo "<li><a href=\"$url\">$file</a></li>";
    }
}

echo "</ul>";





// Chemin du répertoire à parcourir
$directory = 'app/accueil/';

// Ouvrir le répertoire
$dir = opendir($directory);

// Lire tous les fichiers et répertoires dans le répertoire
while (($file = readdir($dir)) !== false) {
    // Exclure les fichiers et répertoires spéciaux
    if ($file != '.' && $file != '..') {
        // Afficher le lien vers chaque fichier
        echo '<a href="' . $directory . $file . '">' . $file . '</a><br>';
    }
}

// Fermer le gestionnaire de répertoire
closedir($dir);

?>