<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\FairePartie;
use PDO;
use PDOException;

/**
 * Class FairePartiePDO
 * Gère les requêtes PDO liées à la table FairePartie.
 */
class FairePartiePDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe FairePartiePDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant des genres à partir de l'identifiant d'un album.
     *
     * @param int $id_album L'identifiant de l'album pour lequel récupérer les identifiants des genres.
     * @return array Retourne un tableau d'identifiants de genres associés à l'album
     */
    public function getIdGenreByIdAlbum(int $id_album): array
    {
        $requete_id_genre = <<<EOF
        select id_genre from FAIRE_PARTIE where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_genre);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
            $les_id_genres = $stmt->fetchAll();
            return $les_id_genres;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return [];
        }
    }

    /**
     * Obtient l'identifiant des albums à partir de l'identifiant d'un genre.
     *
     * @param int $id_genre L'identifiant du genre pour lequel récupérer les identifiants des albums.
     * @return array Retourne un tableau d'identifiants d'albums associés au genre
     */
    public function getIdAlbumByIdGenre(int $id_genre): array
    {
        $requete_id_album = <<<EOF
        select id_album from FAIRE_PARTIE where id_genre = :id_genre;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_album);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
            $les_id_albums = $stmt->fetchAll();
            return $les_id_albums;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return [];
        }
    }

    /**
     * Ajoute un nouvel faire_partie à la base de données.
     *
     * @param int    $id_album    L'identifiant de l'album associé à la musique.
     * @param int    $id_genre    L'identifiant du genre associé à l'album.
     */
    public function ajouterFairePartie(int $id_album, int $id_genre): void
    {
        $insertion_faire_partie = <<<EOF
        insert into FAIRE_PARTIE (id_album, id_genre) values (:id_album, :id_genre);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_faire_partie);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Supprime la liaison des genres associés à un album dans la table.
     * 
     * @param int $id_album L'identifiant de l'album pour lequel supprimer ses genres.
     */
    public function supprimerGenresByIdAlbum(int $id_album): void
    {
        $requete_suppression_genres = <<<EOF
        delete from FAIRE_PARTIE where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_genres);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }
}