<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\RealiserPar;
use PDO;
use PDOException;

/**
 * Class RealiserPDO
 * Gère les requêtes PDO liées à la table Realiser.
 */
class RealiserParPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe RealiserPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant des artistes à partir de l'identifiant d'un album.
     *
     * @param int $id_album L'identifiant de l'album pour lequel récupérer les identifiants des artistes.
     * @return array Retourne un tableau d'identifiants d'artistes ayant réalisés l'album
     */
    public function getIdArtistesByIdAlbum(int $id_album): array
    {
        $requete_id_artiste = <<<EOF
        select id_artiste from REALISER_PAR where id_album = :id_album;
        EOF;
        $les_id_artistes = array();
        try{
            $stmt = $this->pdo->prepare($requete_id_artiste);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
            $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultats as $resultat) {
                array_push($les_id_artistes , $resultat['id_artiste']);
            }
            return $les_id_artistes;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_id_artistes;
        }
    }

    /**
     * Obtient l'identifiant des albums à partir de l'identifiant d'un artiste.
     *
     * @param int $id_artiste L'identifiant de l'artiste pour lequel récupérer les identifiants des albums.
     * @return array Retourne un tableau d'identifiants d'albums ayant été réalisés par l'artiste
     */
    public function getIdAlbumsByIdArtiste(int $id_artiste): array
    {
        $requete_id_album = <<<EOF
        select id_artiste from REALISER_PAR where id_artiste = :id_artiste;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_album);
            $stmt->bindParam("id_artiste", $id_artiste, PDO::PARAM_INT);
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
     * Ajoute un nouvel realiser à la base de données.
     *
     * @param int    $id_album    L'identifiant de l'album.
     * @param int    $id_artiste    L'identifiant de l'artiste.
     */
    public function ajouterRealiser(int $id_album, int $id_artiste): void
    {
        $insertion_realiser = <<<EOF
        insert into REALISER_PAR (id_album, id_artiste) values (:id_album, :id_artiste);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_realiser);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->bindParam("id_artiste", $id_artiste, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Supprime l'album d'un artiste dans la table.
     * 
     * @param int $id_album L'identifiant de l'album pour lequel le supprimer.
     */
    public function supprimerAlbumByIdAlbum(int $id_album): void
    {
        $requete_suppression_album = <<<EOF
        delete from REALISER_PAR where id_album = :id_album;
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