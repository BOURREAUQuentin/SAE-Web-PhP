<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\Album;
use Modele\modele_php\Musique;
use PDO;
use PDOException;

/**
 * Class AlbumPDO
 * Gère les requêtes PDO liées à la table Album.
 */
class AlbumPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe AlbumPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant maximum d'album dans la table.
     *
     * @return int L'identifiant maximum d'album.
     */
    public function getMaxIdAlbum(): int
    {
        $requete_max_id = <<<EOF
        select max(id_album) maxIdAlbum from ALBUM;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_max_id);
            $stmt->execute();
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultat["maxIdAlbum"];
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
        }
    }

    /**
     * Ajoute un nouvel album à la base de données.
     *
     * @param string $titre Le nom de l'album à ajouter.
     * @param string $annee_sortie L'année de sortie de l'album à ajouter.
     * @param int    $id_image    L'identifiant de l'image associée à l'album.
     * 
     * @return int L'identifiant du nouvel album.
     */
    public function ajouterAlbum(string $titre, string $annee_sortie, int $id_image): int
    {
        $new_id_album = $this->getMaxIdAlbum() + 1;
        $insertion_album = <<<EOF
        insert into ALBUM (id_album, titre, annee_sortie, id_image) values (:id_album, :titre, :annee_sortie, :id_image);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_album);
            $stmt->bindParam("id_album", $new_id_album, PDO::PARAM_INT);
            $stmt->bindParam("titre", $titre, PDO::PARAM_STR);
            $stmt->bindParam("annee_sortie", $annee_sortie, PDO::PARAM_STR);
            $stmt->bindParam("id_image", $id_image, PDO::PARAM_INT);
            $stmt->execute();
            return $new_id_album;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
        }
    }

    /**
     * Met à jour le nom d'un album dans la base de données.
     *
     * @param int    $id_album   L'identifiant de l'album à mettre à jour.
     * @param string $nouveau_titre  Le nouveau nom de l'album.
     * @param int $nouvelle_annee_sortie  La nouvelle année de sortie de l'album.
     */
    public function mettreAJourInfosAlbum(int $id_album, string $nouveau_titre, int $nouvelle_annee_sortie): void
    {
        $maj_album = <<<EOF
        update ALBUM set titre = :nouveau_titre, annee_sortie = :nouvelle_annee_sortie where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($maj_album);
            $stmt->bindParam("nouveau_titre", $nouveau_titre, PDO::PARAM_STR);
            $stmt->bindParam("nouvelle_annee_sortie", $nouvelle_annee_sortie, PDO::PARAM_INT);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Obtient l'album dans la table.
     *
     * @param int    $id_album   L'identifiant de l'album à rechercher.
     * 
     * @return Album L'album correspondant à l'identifiant donné, ou null si l'album n'est pas trouvée.
     */
    public function getAlbumByIdAlbum(int $id_album): ?Album
    {
        $requete_album = <<<EOF
        select id_album, titre, annee_sortie, id_image from ALBUM where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_album);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultat) {
                // retourne une instance de la classe Album avec les données récupérées
                return new Album($resultat['id_album'], $resultat['titre'], $resultat['annee_sortie'], $resultat['id_image']);
            } else {
                // Aucun album trouvé avec l'identifiant donné
                return null;
            }
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return null;
        }
    }

    /**
     * Obtient la liste des musiques d'un album spécifique.
     *
     * @param int $id_album L'identifiant de l'album pour lequel calculer la liste des musiques.
     *
     * @return array La liste des musiques d'un album ou [] en cas d'erreur.
     */
    public function getMusiquesByIdAlbum(int $id_album): array
    {
        $requete_musiques = <<<EOF
        select id_musique, nom_musique, duree_musique, son_musique, nb_streams, id_album from ALBUM natural join MUSIQUE where id_album = :id_album;
        EOF;
        $les_musiques = array();
        try{
            $stmt = $this->pdo->prepare($requete_musiques);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
            $les_musiques_album = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($les_musiques_album as $musique_album){
                array_push($les_musiques, new Musique($musique_album['id_musique'], $musique_album['nom_musique'], $musique_album['duree_musique'], $musique_album['son_musique'], $musique_album['nb_streams'], $musique_album['id_album']));
            }
            return $les_musiques;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_musiques;
        }
    }

    /**
     * Obtient la durée totale d'un album spécifique.
     *
     * @param int $id_album L'identifiant de l'album pour lequel calculer la durée totale.
     *
     * @return string La durée totale d'un album ou 0 en cas d'erreur.
     */
    public function getDureeTotalByIdAlbum(int $id_album): string
    {
        $requete_durees_musiques_album = <<<EOF
        select duree_musique from ALBUM natural join MUSIQUE where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_durees_musiques_album);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
            $durees_musiques = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $duree_total_secondes = 0;
            foreach ($durees_musiques as $duree_musique){
                list($minutes, $secondes) = explode(":", $duree_musique["duree_musique"]);
                $duree_secondes_musique = ($minutes * 60) + $secondes; // conversion des minutes en secondes et ajout aux secondes
                $duree_total_secondes += intval($duree_secondes_musique);
            }
            // calcul des minutes et des secondes
            $duree_total_heures = floor($duree_total_secondes / 3600);
            $duree_total_secondes %= 3600;
            $duree_total_minutes = floor($duree_total_secondes / 60);
            $duree_total_secondes %= 60;
            $duree_total_lisible = sprintf("%02d:%02d:%02d", $duree_total_heures, $duree_total_minutes, $duree_total_secondes); // formattage du résultat
            return $duree_total_lisible;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return "00:00";
        }
    }

    /**
     * Obtient la liste des albums dans la table.
     * 
     * @return array La liste des albums.
     */
    public function getAlbums(): array
    {
        $requete_albums = <<<EOF
        select id_album, titre, annee_sortie, id_image from ALBUM;
        EOF;
        $les_albums = array();
        try{
            $stmt = $this->pdo->prepare($requete_albums);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $album) {
                array_push($les_albums, new Album($album['id_album'], $album['titre'], $album['annee_sortie'], $album['id_image']));
            }
            return $les_albums;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_albums;
        }
    }

    /**
     * Obtient la liste des albums d'un genre dans la table.
     * 
     * @param int $id_genre L'identifiant du genre pour lequel récupérer la liste des albums.
     * 
     * @return array La liste des albums d'un genre.
     */
    public function getAlbumsByIdGenre(int $id_genre): array
    {
        $requete_albums_genre = <<<EOF
        select id_album, titre, annee_sortie, id_image from ALBUM natural join FAIRE_PARTIE where id_genre = :id_genre;
        EOF;
        $les_albums_genre = array();
        try{
            $stmt = $this->pdo->prepare($requete_albums_genre);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $album) {
                array_push($les_albums_genre, new Album($album['id_album'], $album['titre'], $album['annee_sortie'], $album['id_image']));
            }
            return $les_albums_genre;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_albums_genre;
        }
    }

    /**
     * Obtient la liste des albums pour la recherche dans la table.
     * 
     * @param string $intitule_recherche L'intitulé de la recherche pour lequel récupérer la liste des albums.
     * 
     * @return array La liste des albums des résultats de la recherche.
     */
    public function getAlbumsByRecherche(string $intitule_recherche): array
    {
        $requete_albums_recherche = <<<EOF
        select id_album, titre, annee_sortie, id_image from ALBUM where titre LIKE :intitule_recherche;
        EOF;
        $les_albums_genre = array();
        try{
            $stmt = $this->pdo->prepare($requete_albums_recherche);
            $intitule_recherche = '%' . $intitule_recherche . '%';
            $stmt->bindParam("intitule_recherche", $intitule_recherche, PDO::PARAM_STR);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $album) {
                array_push($les_albums_genre, new Album($album['id_album'], $album['titre'], $album['annee_sortie'], $album['id_image']));
            }
            return $les_albums_genre;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_albums_genre;
        }
    }

    /**
     * Obtient la liste des albums pour le filtre d'année choisi dans la table.
     * 
     * @param string $annee L'année choisie pour lequel récupérer la liste des albums.
     * 
     * @return array La liste des albums des résultats du filtre.
     */
    public function getAlbumsByFiltreAnnee(string $annee): array
    {
        $requete_albums_recherche = <<<EOF
        select id_album, titre, annee_sortie, id_image from ALBUM where annee_sortie >= :annee and annee_sortie < :annee_max;
        EOF;
        $les_albums_genre = array();
        try{
            $stmt = $this->pdo->prepare($requete_albums_recherche);
            $stmt->bindParam("annee", $annee, PDO::PARAM_STR);
            $annee_max = strval((intval($annee)+10));
            $stmt->bindParam("annee_max", $annee_max, PDO::PARAM_STR);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $album) {
                array_push($les_albums_genre, new Album($album['id_album'], $album['titre'], $album['annee_sortie'], $album['id_image']));
            }
            return $les_albums_genre;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_albums_genre;
        }
    }

    /**
     * Supprime l'album associé à l'id album dans la table.
     * 
     * @param int $id_album L'identifiant de l'album pour lequel supprimer l'album.
     */
    public function supprimerAlbumByIdAlbum(int $id_album): void
    {
        $requete_suppression_album = <<<EOF
        delete from ALBUM where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_album);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }
}