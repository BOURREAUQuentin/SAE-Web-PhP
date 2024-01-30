<?php

/**
 * Class UtilisateurPDO
 * Gère les requêtes PDO liées à la table Utilisateur.
 */
class UtilisateurPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe UtilisateurPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant maximum d'un utilisateur dans la table.
     *
     * @return int L'identifiant maximum d'un utilisateur.
     */
    public function getMaxIdUtilisateur(): int
    {
        $requete_max_id = <<<EOF
        select max(id_utilisateur) maxIdUtilisateur from UTILISATEUR;
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
     * Ajoute un nouvel utilisateur à la base de données.
     *
     * @param string $nom_utilisateur Le nom de l'utilisateur à ajouter.
     * @param string    $mail_utilisateur    Le mail associé à l'utilisateur.
     * @param string    $mdp    Le mot de passe associé à l'utilisateur.
     */
    public function ajouterUtilisateur(string $nom_utilisateur, string $mail_utilisateur, string $mdp): void
    {
        $new_id_utilisateur = $this->getMaxIdUtilisateur() + 1;
        $insertion_utilisateur = <<<EOF
        insert into UTILISATEUR (id_utilisateur, nom_utilisateur, mail_utilisateur, mdp) values (:id_utilisateur, :nom_utilisateur, :mail_utilisateur, :mdp);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_utilisateur);
            $stmt->bindParam("id_utilisateur", $new_id_utilisateur, PDO::PARAM_INT);
            $stmt->bindParam("nom_utilisateur", $nom_utilisateur, PDO::PARAM_STR);
            $stmt->bindParam("mail_utilisateur", $mail_utilisateur, PDO::PARAM_STR);$
            $stmt->bindParam("mdp", $mdp, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Met à jour les infos d'un utilisateur dans la base de données.
     *
     * @param int    $id_utilisateur   L'identifiant de l'utilisateur à mettre à jour.
     * @param string $nouveau_nom  Le nouveau nom d'utilisateur de l'utilisateur.
     * @param string $nouveau_mail  Le nouveau mail de l'utilisateur.
     * @param string $nouveau_mdp  Le nouveau mot de passe de l'utilisateur.
     */
    public function mettreAJourNomUtilisateur(int $id_utilisateur, string $nouveau_nom, string $nouveau_mail, string $nouveau_mdp): void
    {
        $maj_artiste = <<<EOF
        update UTILISATEUR set nom_utilisateur = :nouveau_nom, mail_utilisateur = :nouveau_mail, mdp = :nouveau_mdp where id_utilisateur = :id_utilisateur;
        EOF;
        try{
            $stmt = $this->pdo->prepare($maj_artiste);
            $stmt->bindParam("nouveau_nom", $nouveau_nom, PDO::PARAM_STR);
            $stmt->bindParam("nouveau_mail", $nouveau_mail, PDO::PARAM_STR);
            $stmt->bindParam("nouveau_mdp", $nouveau_mdp, PDO::PARAM_STR);
            $stmt->bindParam("id_utilisateur", $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

}