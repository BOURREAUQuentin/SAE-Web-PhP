<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\Contenir;
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
     * @return bool Retourne true si la musique est bien ajoutée, sinon false (cas d'erreur que la musique est déjà présente dans la playlist)
     */
    public function ajouterContenir(int $id_musique, int $id_playlist): bool
    {
        // vérification de si la musique est déjà dans la playlist
        if ($this->estMusiqueDansPlaylist($id_musique, $id_playlist)) {
            return false;
        }
        $insertion_contenir = <<<EOF
        insert into CONTENIR (id_musique, id_playlist) values (:id_musique, :id_playlist);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_contenir);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si une musique est déjà dans une playlist.
     *
     * @param int $id_musique L'identifiant de la musique.
     * @param int $id_playlist L'identifiant de la playlist.
     *
     * @return bool Retourne true si la musique est déjà dans la playlist, sinon false.
     *
     */
    private function estMusiqueDansPlaylist($id_musique, $id_playlist): bool
    {
        $requete_est_dans_contenir = <<<EOF
        select * from CONTENIR where id_musique = :id_musique and id_playlist = :id_playlist;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_est_dans_contenir);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
            $resultat = $stmt->fetch();
            if ($resultat != null){
                return true;
            }
            return false;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un contenir à la base de données.
     *
     * @param int    $id_musique    L'identifiant de la musique associée à la playlist.
     * @param int    $id_playlist    L'identifiant de la playlist associée à la musique.
     */
    public function supprimerContenir(int $id_musique, int $id_playlist): void
    {
        $suppression_contenir = <<<EOF
        delete from CONTENIR where id_musique = :id_musique and id_playlist = :id_playlist;
        EOF;
        try{
            $stmt = $this->pdo->prepare($suppression_contenir);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Supprime la liste des musiques de playlists associées à un album dans la table.
     * 
     * @param int $id_album L'identifiant de l'album pour lequel supprimer la liste des musiques de playlists.
     */
    public function supprimerMusiquesPlaylistsByIdAlbum(int $id_album): void
    {
        $requete_suppression_musiques = <<<EOF
        delete from CONTENIR where id_musique in (select id_musique from MUSIQUE where id_album = :id_album);
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_musiques);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Supprime la liste des musiques de playlists dans la table.
     * 
     * @param int $id_musique L'identifiant de la musique pour lequel la supprimer dans les playlists.
     */
    public function supprimerMusiquesPlaylistsByIdMusique(int $id_musique): void
    {
        $requete_suppression_musiques = <<<EOF
        delete from CONTENIR where id_musique = :id_musique;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_musiques);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Supprime la liste des musiques d'une playlist dans la table.
     * 
     * @param int $id_playlist L'identifiant de la playlist pour lequel supprimer la liste des musiques de celle-ci.
     */
    public function supprimerMusiquesPlaylistsByIdPlaylist(int $id_playlist): void
    {
        $requete_suppression_musiques = <<<EOF
        delete from CONTENIR where id_playlist = :id_playlist;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_musiques);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }
}