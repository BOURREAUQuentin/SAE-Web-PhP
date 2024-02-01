<?php

declare(strict_types=1);
require 'Classes/autoloader.php';
Autoloader::register();

// import des différentes classes nécessaires
use Data\modele_bd\AlbumPDO;

// Connection en utlisant la connexion PDO avec le moteur en prefixe
$pdo = new PDO('sqlite:sae_php.sqlite');
// Permet de gérer le niveau des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// // initialisation des différentes classes en PDO
// $albumPDO = new AlbumPDO($pdo);

// echo "<h1>Liste des albums</h1>" . PHP_EOL;
// $les_albums = $albumPDO->getAlbums();
// foreach ($les_albums as $album){
//     echo "<p>" . $album->getTitre() . "</p>" . PHP_EOL;
// }

// Charger le contenu du fichier YAML
$fileContent = file_get_contents('fixtures/extrait.yml');

// Vérifier si le chargement a réussi
if ($fileContent === false) {
    die('Erreur de chargement du fichier YAML.');
}

// Convertir le fichier YAML en tableau associatif
$lines = explode("\n", $fileContent);
$data = [];
$currentAlbum = [];

foreach ($lines as $line) {
    $line = trim($line);

    // Ignorer les lignes vides
    if (empty($line)) {
        continue;
    }

    // Identifier le début d'un nouvel album
    if (strpos($line, '- by:') === 0) {
        // Ajouter l'album actuel au tableau
        if (!empty($currentAlbum)) {
            $data[] = $currentAlbum;
            $currentAlbum = [];
        }
    }

    // Extraire les clés et les valeurs
    list($key, $value) = explode(':', $line, 2);
    $key = trim($key);
    $value = trim($value);

    // Gérer le cas où la valeur est un tableau
    if ($key === 'genre') {
        $currentAlbum[$key] = array_map('trim', explode(',', substr($value, 1, -1)));
    } else {
        $currentAlbum[$key] = $value;
    }
}

// Ajouter le dernier album au tableau
if (!empty($currentAlbum)) {
    $data[] = $currentAlbum;
}

// Parcourir les données
foreach ($data as $album) {
    // Afficher les informations de chaque album
    echo "Artiste: " . $album['by'] . PHP_EOL;
    echo "Titre: " . $album['title'] . PHP_EOL;
    echo "Genre: " . implode(', ', $album['genre']) . PHP_EOL;
    echo "Année de sortie: " . $album['releaseYear'] . PHP_EOL;
    echo "Image: " . ($album['img'] ?? 'Aucune image disponible') . PHP_EOL;
    echo "--------------------------------------" . PHP_EOL;
}

?>
