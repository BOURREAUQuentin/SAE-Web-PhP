<?php

declare(strict_types=1);
namespace Data\modele_bd;
use Data\modele_php\Contenir;
use PDO;
use PDOException;

/**
 * Class ContenirPDO
 * Gère les requêtes PDO liées à la table Contenir.
 */
class ContenirPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe ContenirPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant des musiques à partir de l'identifiant d'une playlist.
     *
     * @param int $id_playlist L'identifiant de la playlist pour lequel récupérer les identifiants des musiques.
     * @return array Retourne un tableau d'identifiants de musiques associés à la playlist
     */
    public function getIdMusiqueByIdPlaylist(int $id_playlist): array
    {
        $requete_id_musique = <<<EOF
        select id_musique from CONTENIR where id_playlist = :id_playlist;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_musique);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
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
     * Ajoute un nouvel contenir à la base de données.
     *
     * @param int    $id_musique    L'identifiant de la musique associée à la playlist.
     * @param int    $id_playlist    L'identifiant de la playlist associée à la musique.
     */
    public function ajouterContenir(int $id_musique, int $id_playlist): void
    {
        $insertion_contenir = <<<EOF
        insert into CONTENIR (id_musique, id_playlist) values (:id_musique, :id_playlist);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_contenir);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }
}