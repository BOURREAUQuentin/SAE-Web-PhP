<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\Liker;
use Modele\modele_php\Musique;
use PDO;
use PDOException;

/**
 * Class LikerPDO
 * Gère les requêtes PDO liées à la table Liker.
 */
class LikerPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe LikerPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant des musiques à partir de l'identifiant d'un utilisateur.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur pour lequel récupérer les identifiants des musiques.
     * @return array Retourne un tableau d'identifiants de musiques associés à l'utilisateur
     */
    public function getIdMusiqueByIdUtilisateur(int $id_utilisateur): array
    {
        $requete_id_musique = <<<EOF
        select id_musique from LIKER where id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_musique);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
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
     * Obtient les musiques à partir de l'identifiant d'un utilisateur.
     *
     * @param int $id_musique L'identifiant de la musique pour laquelle récupérer les identifiants des utilisateurs.
     * @return array Retourne un tableau d'identifiants d'utilisateurs associés à la musique
     */
    public function getMusiqueByUtilisateur(int $id_utilisateur): array
    {
        $requete_id_musique = <<<EOF
        select id_musique, nom_musique, duree_musique, son_musique, nb_streams, id_album from LIKER natural join MUSIQUE where id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_musique);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            
            $les_id_musiques = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $les_musiques = array();
            foreach ($les_id_musiques as $musique){
                array_push($les_musiques, new Musique($musique['id_musique'], $musique['nom_musique'], $musique['duree_musique'], $musique['son_musique'], $musique['nb_streams'], $musique['id_album']));
            }
            return $les_musiques;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return [];
        }
    }

    /**
     * Ajoute un nouvel like à la base de données.
     *
     * @param int    $id_musique    L'identifiant de la musique associée à l'utilisateur.
     * @param int    $id_utilisateur    L'identifiant de l'utilisateur associé à la musique.
     */
    public function ajouterLiker(int $id_musique, int $id_utilisateur): void
    {
        $insertion_like = <<<EOF
        insert into LIKER (id_musique, id_utilisateur) values (:id_musique, :id_utilisateur);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_like);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Supprime un like de la base de données.
     *
     * @param int    $id_musique    L'identifiant de la musique associée à l'utilisateur.
     * @param int    $id_utilisateur    L'identifiant de l'utilisateur associé à la musique.
     */
    public function supprimerLiker(int $id_musique, int $id_utilisateur): void
    {
        $suppression_like = <<<EOF
        delete from LIKER where id_musique = :id_musique and id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($suppression_like);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Vérifie si un utilisateur a liké une musique.
     *
     * @param int    $id_musique    L'identifiant de la musique associée à l'utilisateur.
     * @param int    $id_utilisateur    L'identifiant de l'utilisateur associé à la musique.
     * @return bool Retourne vrai si l'utilisateur a liké la musique, faux sinon.
     */
    public function verifieMusiqueLiker(int $id_musique, int $id_utilisateur): bool
    {
        $requete_like = <<<EOF
        select * from LIKER where id_musique = :id_musique and id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_like);
            $stmt->bindParam("id_musique", $id_musique, PDO::PARAM_INT);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            $like = $stmt->fetch();
            return $like != null;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * Supprime la liste des likes associés à une musique donc un album dans la table.
     * 
     * @param int $id_album L'identifiant de l'album pour lequel supprimer la liste des likes.
     */
    public function supprimerLikesByIdAlbum(int $id_album): void
    {
        $requete_suppression_musiques = <<<EOF
        delete from LIKER where id_musique in (select id_musique from MUSIQUE where id_album = :id_album);
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
     * Supprime la liste des likes associés à une musique dans la table.
     * 
     * @param int $id_musique L'identifiant de la musique pour lequel supprimer la liste des likes.
     */
    public function supprimerLikesByIdMusique(int $id_musique): void
    {
        $requete_suppression_musiques = <<<EOF
        delete from LIKER where id_musique = :id_musique;
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
     * Supprime la liste des likes associés à un utilisateur dans la table.
     * 
     * @param int $id_utilisateur L'identifiant de l'utilisateur pour lequel supprimer la liste des likes.
     */
    public function supprimerLikesByIdUtilisateur(int $id_utilisateur): void
    {
        $requete_suppression_musiques = <<<EOF
        delete from LIKER where id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_musiques);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Obtient la durée totale des titres likés.
     *
     * @param int $id_utilisateur L'identifiant de la playlist pour lequel calculer la durée totale.
     *
     * @return string La durée totale d'une playlist ou 0 en cas d'erreur.
     */
    public function getDureeTotalByIdUtilisateur(int $id_utilisateur): string
    {
        $requete_durees_titres_likes = <<<EOF
        select duree_musique from LIKER natural join MUSIQUE where id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_durees_titres_likes);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            $durees_titres_likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $duree_total_secondes = 0;
            foreach ($durees_titres_likes as $duree_titre_like){
                list($minutes, $secondes) = explode(":", $duree_titre_like["duree_musique"]);
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