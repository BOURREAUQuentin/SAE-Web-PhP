<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\Playlist;
use Modele\modele_php\Musique;
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
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultat["maxIdPlaylist"];
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
     * 
     * @return int   L'identifiant de la nouvelle playlist
     */
    public function creerPlaylist(string $nom_playlist, int $id_image, int $id_utilisateur): int
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
            return $new_id_playlist;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
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

    /**
     * Obtient la liste des musiques d'une playlist dans la table.
     * 
     * @param int $id_playlist L'identifiant d'une playlist pour lequel récupérer la liste des musiques.
     * 
     * @return array La liste des musiques d'une playlist.
     */
    public function getMusiquesByIdPlaylist(int $id_playlist): array
    {
        $requete_musiques_playlist = <<<EOF
        select id_musique, nom_musique, duree_musique, son_musique, nb_streams, id_album from MUSIQUE natural join CONTENIR where id_playlist = :id_playlist;
        EOF;
        $les_musiques_playlist = array();
        try{
            $stmt = $this->pdo->prepare($requete_musiques_playlist);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $musique) {
                array_push($les_musiques_playlist, new Musique($musique['id_musique'], $musique['nom_musique'], $musique['duree_musique'], $musique['son_musique'], $musique['nb_streams'], $musique['id_album']));
            }
            return $les_musiques_playlist;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_musiques_playlist;
        }
    }

    /**
     * Obtient la liste des playlists d'un artiste dans la table.
     * 
     * @param string $nom_utilisateur Le nom d'utilisateur pour lequel récupérer la liste des playlists.
     * 
     * @return array La liste des playlist d'un utilisateur.
     */
    public function getPlaylistsByNomUtilisateur(string $nom_utilisateur): array
    {
        $requete_playlists_utilisateur = <<<EOF
        select id_playlist, nom_playlist, id_image, id_utilisateur from PLAYLIST natural join UTILISATEUR where nom_utilisateur = :nom_utilisateur;
        EOF;
        $les_playlists_utilisateur = array();
        try{
            $stmt = $this->pdo->prepare($requete_playlists_utilisateur);
            $stmt->bindParam("nom_utilisateur", $nom_utilisateur, PDO::PARAM_STR);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $playlist) {
                array_push($les_playlists_utilisateur, new Playlist($playlist['id_playlist'], $playlist['nom_playlist'], $playlist['id_image'], $playlist['id_utilisateur']));
            }
            return $les_playlists_utilisateur;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_playlists_utilisateur;
        }
    }

    /**
     * Supprime les playlists d'un utilisateur dans la table.
     * 
     * @param int $id_utilisateur L'identifiant de l'utilisateur pour lequel supprimer les playlists.
     */
    public function supprimerPlaylistsByIdUtilisateur(int $id_utilisateur): void
    {
        $requete_suppression_playlists = <<<EOF
        delete from PLAYLIST where id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_playlists);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Supprime la playlist associée à l'id playlist dans la table.
     * 
     * @param int $id_playlist L'identifiant de la playlist pour lequel supprimer la playlist.
     */
    public function supprimerPlaylistByIdPlaylist(int $id_playlist): void
    {
        $requete_suppression_playlist = <<<EOF
        delete from PLAYLIST where id_playlist = :id_playlist;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_playlist);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Obtient la liste des playlists de l'utilisateur ne contenant pas la musique dans la table.
     * 
     * @param int $id_utilisateur L'identifiant de l'utilisateur pour lequel les playlists ne contenant la musique.
     * @param int $id_musique L'identifiant d'une musique pour lequel récupérer la liste des playlists de l'utilisateur où elle n'est pas encore dedans.
     * 
     * @return array La liste des playlists de l'utilisateur où une musique n'est pas encore dedans.
     */
    public function getPlaylistsUtilisateurSansMusiqueByIdMusique(int $id_utilisateur, int $id_musique): array
    {
        $requete_playlists_sans_musique = <<<EOF
        select p.id_playlist, p.nom_playlist, p.id_image, p.id_utilisateur from PLAYLIST p where p.id_utilisateur = :id_utilisateur and p.id_playlist not in (select c.id_playlist from CONTENIR c where c.id_musique = :id_musique);
        EOF;
        $les_playlists_sans_musique = array();
        try{
            $stmt = $this->pdo->prepare($requete_playlists_sans_musique);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $playlist) {
                array_push($les_playlists_sans_musique, new Playlist($playlist['id_playlist'], $playlist['nom_playlist'], $playlist['id_image'], $playlist['id_utilisateur']));
            }
            return $les_playlists_sans_musique;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_playlists_sans_musique;
        }
    }

    /**
     * Obtient la durée totale d'une playlist spécifique.
     *
     * @param int $id_playlist L'identifiant de la playlist pour lequel calculer la durée totale.
     *
     * @return string La durée totale d'une playlist ou 0 en cas d'erreur.
     */
    public function getDureeTotalByIdPlaylist(int $id_playlist): string
    {
        $requete_durees_musiques_playlist = <<<EOF
        select duree_musique from PLAYLIST natural join CONTENIR natural join MUSIQUE where id_playlist = :id_playlist;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_durees_musiques_playlist);
            $stmt->bindParam("id_playlist", $id_playlist, PDO::PARAM_INT);
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
}