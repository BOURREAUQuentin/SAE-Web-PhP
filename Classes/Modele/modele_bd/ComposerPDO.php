<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\Composer;
use PDO;
use PDOException;

/**
 * Class ComposerPDO
 * Gère les requêtes PDO liées à la table Composer.
 */
class ComposerPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe ComposerPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant des musiques à partir de l'identifiant d'un album.
     *
     * @param int $id_album L'identifiant de l'album pour lequel récupérer les identifiants des musiques.
     * @return array Retourne un tableau d'identifiants de musiques associés à l'album
     */
    public function getIdMusiqueByIdAlbum(int $id_album): array
    {
        $requete_id_musique = <<<EOF
        select id_musique from COMPOSER where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_musique);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
            $les_id_musiques = $stmt->fetchAll();
            return $les_id_musiques;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return [];
        }
    }

    /**
     * Obtient l'identifiant de l'album à partir de l'identifiant d'une musique.
     *
     * @param int $id_musique L'identifiant de la musique pour lequel récupérer l'identifiant de l'album associé.
     * @return int Retourne l'identifiant de l'album associés à la musique
     */
    public function getIdAlbumByIdMusique(int $id_musique): int
    {
        $requete_id_musique = <<<EOF
        select id_album from COMPOSER where id_musique = :id_musique;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_musique);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->execute();
            $id_album_trouve = $stmt->fetch();
            return $id_album_trouve;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
        }
    }

    /**
     * Ajoute un nouvel composer à la base de données.
     *
     * @param int    $id_musique    L'identifiant de la musique associée à l'album.
     * @param int    $id_album    L'identifiant de l'album associé à la musique.
     */
    public function ajouterComposer(int $id_musique, int $id_album): void
    {
        $insertion_composer = <<<EOF
        insert into COMPOSER (id_musique, id_album) values (:id_musique, :id_album);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_composer);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }
}