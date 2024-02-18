<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\Noter;
use PDO;
use PDOException;

/**
 * Class NoterPDO
 * Gère les requêtes PDO liées à la table Noter.
 */
class NoterPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe NoterPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient la note à partir de l'identifiant d'un album et d'un utilisateur.
     *
     * @param int $id_album L'identifiant de l'album pour lequel récupérer la note.
     * @param int $id_utilisateur L'identifiant de l'utilisateur pour lequel récupérer la note.
     * @return int|null Retourne la note de l'utilisateur sur l'album s'il l'a déjà noté, sinon null
     */
    public function getNoteByIdAlbumIdUtilisateur(int $id_album, int $id_utilisateur): ?int
    {
        $requete_note = <<<EOF
            SELECT note FROM NOTER WHERE id_album = :id_album AND id_utilisateur = :id_utilisateur;
        EOF;
        try {
            $stmt = $this->pdo->prepare($requete_note);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            $note = $stmt->fetchColumn();
            return $note !== false ? (int)$note : 0;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            return 0;
        }
    }
    
    

    /**
     * Ajoute une nouvelle note à la base de données.
     *
     * @param int    $id_album    L'identifiant de l'album associée à l'utilisateur.
     * @param int    $id_utilisateur    L'identifiant de l'utilisateur qui a noté l'album.
     * @param int    $note    La note de l'utilisateur.
     */
    public function ajouterNoter(int $id_album, int $id_utilisateur, int $note): void
    {
        $insertion_like = <<<EOF
        insert into NOTER (id_album, id_utilisateur, note) values (:id_album, :id_utilisateur, :note);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_like);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->bindParam("note", $note, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Calcule la moyenne des notes attribuées à un album spécifique.
     *
     * @param int $id_album L'identifiant de l'album pour lequel calculer la moyenne des notes.
     *
     * @return float|null La moyenne des notes attribuées à l'album ou null si aucune note n'a été attribuée.
     */
    public function getMoyenneNoteByIdAlbum(int $id_album): float
    {
        $requete_moyenne_notes_album = <<<EOF
        SELECT SUM(note) as sommeNotes FROM NOTER WHERE id_album = :id_album;
    EOF;
        $requete_nb_notes_album = <<<EOF
        SELECT COUNT(note) as nbNotes FROM NOTER WHERE id_album = :id_album;
    EOF;
        try {
            // Calcul de la somme des notes
            $stmt = $this->pdo->prepare($requete_moyenne_notes_album);
            $stmt->bindParam(":id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
            $resultat_somme_notes = $stmt->fetch();
    
            // Calcul du nombre de notes
            $stmt2 = $this->pdo->prepare($requete_nb_notes_album);
            $stmt2->bindParam(":id_album", $id_album, PDO::PARAM_INT);
            $stmt2->execute();
            $resultat_nb_notes = $stmt2->fetch();
    
            // Vérifie si des notes existent avant de calculer la moyenne
            $somme_notes = $resultat_somme_notes['sommeNotes'];
            $nb_notes = $resultat_nb_notes['nbNotes'];
    
            if ($nb_notes > 0) {
                return $somme_notes / $nb_notes;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            return 0;
        }
    }
    

    /**
     * Supprime les notes d'un album dans la table.
     * 
     * @param int $id_album L'identifiant de l'album pour lequel supprimer les notes.
     */
    public function supprimerNotesByIdAlbum(int $id_album): void
    {
        $requete_suppression_notes = <<<EOF
        delete from NOTER where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_notes);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Supprime les notes d'un utilisateur dans la table.
     * 
     * @param int $id_utilisateur L'identifiant de l'utilisateur pour lequel supprimer les notes.
     */
    public function supprimerNotesByIdUtilisateur(int $id_utilisateur): void
    {
        $requete_suppression_notes = <<<EOF
        delete from NOTER where id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_notes);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Met à jour la note d'un utilisateur pour un album.
     * 
     * @param int $id_album L'identifiant de l'album pour lequel mettre à jour la note.
     * @param int $id_utilisateur L'identifiant de l'utilisateur pour lequel mettre à jour la note.
     * @param int $note La note à mettre à jour.
     */
    public function mettreAJourNote(int $id_album, int $id_utilisateur, int $note): void
    {
        $requete_mise_a_jour_note = <<<EOF
        update NOTER set note = :note where id_album = :id_album and id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_mise_a_jour_note);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->bindParam("note", $note, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Nombre de personne ayant noté un album.
     * 
     * @param int $id_album L'identifiant de l'album pour lequel supprimer la note.
     */
    public function getNbPersonneAyantNote(int $id_album): int
    {
        $requete_nb_personne_ayant_note = <<<EOF
        select count(id_utilisateur) as nbPersonne from NOTER where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_nb_personne_ayant_note);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
            $resultat = $stmt->fetch();
            return $resultat['nbPersonne'];
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
        }
    }
}