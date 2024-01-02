<?php
// data.php
function getData(){
 // tableau associatif des données
 $data = array(
 'titre_doc' => 'Bibliotheque',
 'titre_page' => 'Liste des livres',
 'date' => date("d/m/Y"),
 // pour simplifier l'exemple, les données sont définies
 // statiquement (généralement elles sont extraites d'une BD)
 'biblio' => array(
 array('titre'=>'N ou M', 'nom'=>'Christie', 'prenom'=>'Agatha'),
 array('titre'=>'1984', 'nom'=>'Orwell', 'prenom'=>'George'),
 array('titre'=>'Dune', 'nom'=> 'Herbert', 'prenom'=>'Frank')
 )
 );
 return $data;
}

