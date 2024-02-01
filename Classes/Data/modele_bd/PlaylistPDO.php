<?php

declare(strict_types=1);
namespace Data\modele_bd;
use Data\modele_php\Playlist;
use PDO;
use PDOException;

/**
 * Class PlaylistPDO
 * Gère les requêtes PDO liées à la table Playlist.
 */
class PlaylistPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe PlaylistPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant maximum d'une playlist dans la table.
     *
     * @return int L'identifiant maximum d'une playlist.
     */
    public function getMaxIdPlaylist(): int
    {
        $requete_max_id = <<<EOF
        select max(id_playlist) maxIdPlaylist from PLAYLIST;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_max_id);
            $stmt->execute();
            $max_id = $stmt->fetch();
            return $max_id;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
        }
    }

    /**
     * Obtient la playlist dans la table.
     *
     * @param int    $id_playlist   L'identifiant de la playlist à rechercher.
     * 
     * @return Playlist La playlist correspondante à l'identifiant donné, ou null si la playlist n'est pas trouvée.
     */
    public function getPlaylistByIdPlaylist(int $id_playlist): ?Playlist
    {
        $requete_playlist = <<<EOF
        select id_playlist, nom_playlist, id_image, id_utilisateur from PLAYLIST where id_playlist = :id_playlist;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_playlist);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultat) {
                // retourne une instance de la classe Playlist avec les données récupérées
                return new Playlist($resultat['id_playlist'], $resultat['nom_playlist'], $resultat['id_image'], $resultat['id_utilisateur']);
            } else {
                // Aucun playlist trouvé avec l'identifiant donné
                return null;
            }
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return null;
        }
    }

    /**
     * Ajoute une nouvelle playlist à la base de données.
     *
     * @param string $nom_playlist Le nom de la playlist à ajouter.
     * @param int    $id_image    L'identifiant de l'image associée à la playlist.
     * @param int    $id_utilisateur    L'identifiant de l'utilisateur associée à la playlist.
     */
    public function ajouterPlaylist(string $nom_playlist, int $id_image, int $id_utilisateur): void
    {
        $new_id_playlist = $this->getMaxIdPlaylist() + 1;
        $insertion_artiste = <<<EOF
        insert into PLAYLIST (id_playlist, nom_playlist, id_image, id_utilisateur) values (:id_playlist, :nom_playlist, :id_image, :id_utilisateur);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_artiste);
            $stmt->bindParam("id_playlist", $new_id_playlist, PDO::PARAM_INT);
            $stmt->bindParam("nom_playlist", $nom_playlist, PDO::PARAM_STR);
            $stmt->bindParam("id_image", $id_image, PDO::PARAM_INT);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Met à jour le nom d'une playlist dans la base de données.
     *
     * @param int    $id_playlist   L'identifiant de la playlist à mettre à jour.
     * @param string $nouveau_nom  Le nouveau nom de la playlist.
     */
    public function mettreAJourNomPlaylist(int $id_playlist, string $nouveau_nom): void
    {
        $maj_playlist = <<<EOF
        update PLAYLIST set nom_playlist = :nouveau_nom where id_playlist = :id_playlist;
        EOF;
        try{
            $stmt = $this->pdo->prepare($maj_playlist);
            $stmt->bindParam("nouveau_nom", $nouveau_nom, PDO::PARAM_STR);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }
}