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
}