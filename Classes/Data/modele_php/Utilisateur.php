<?php

declare(strict_types=1);
namespace Data\modele_php;

/**
 * Class Utilisateur
 * Représente un utilisateur avec ses propriétés et méthodes associées.
 */
class Utilisateur
{
    /**
     * @var int $id_utilisateur L'identifiant unique de l'utilisateur.
     */
    private $id_utilisateur;

    /**
     * @var string $nom_utilisateur Le nom de l'utilisateur.
     */
    private $nom_utilisateur;

    /**
     * @var string $mail_utilisateur Le mail de l'utilisateur.
     */
    private $mail_utilisateur;

    /**
     * @var string $mdp Le mot de passe de l'utilisateur.
     */
    private $mdp;

    /**
     * @var string $admin Si l'utilisateur est admin.
     */
    private $admin;

    /**
     * Constructeur de la classe Utilisateur.
     *
     * @param int    $id_utilisateur  L'identifiant unique de l'utilisateur.
     * @param string $nom_utilisateur Le nom de l'utilisateur.
     * @param string $mail_utilisateur Le mail de l'utilisateur.
     * @param string $mdp             Le mot de passe de l'utilisateur.
     * @param string $admin             Si l'utilisateur est admin.
     */
    public function __construct(int $id_utilisateur, string $nom_utilisateur, string $mail_utilisateur, string $mdp, string $admin)
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_utilisateur = $nom_utilisateur;
        $this->mail_utilisateur = $mail_utilisateur;
        $this->mdp = $mdp;
        $this->admin = $admin;
    }

    /**
     * Obtient l'identifiant de l'utilisateur.
     *
     * @return int L'identifiant de l'utilisateur.
     */
    public function getIdUtilisateur(): int
    {
        return $this->id_utilisateur;
    }

    /**
     * Obtient le nom de l'utilisateur.
     *
     * @return string Le nom de l'utilisateur.
     */
    public function getNomUtilisateur(): string
    {
        return $this->nom_utilisateur;
    }

    /**
     * Obtient le mail de l'utilisateur.
     *
     * @return string Le mail de l'utilisateur.
     */
    public function getMailUtilisateur(): string
    {
        return $this->mail_utilisateur;
    }

    /**
     * Obtient le mot de passe de l'utilisateur.
     *
     * @return string Le mot de passe de l'utilisateur.
     */
    public function getMdp(): string
    {
        return $this->mdp;
    }

    /**
     * Obtient si l'utilisateur est admin.
     *
     * @return bool Si l'utilisateur est admin.
     */
    public function isAdmin(): bool
    {
        return $this->admin == "O";
    }

    /**
     * Modifie le nom de l'utilisateur.
     *
     * @param string $nom_utilisateur Le nouveau nom de l'utilisateur.
     */
    public function setNomUtilisateur(string $nom_utilisateur): void
    {
        $this->nom_utilisateur = $nom_utilisateur;
    }

    /**
     * Modifie le mail de l'utilisateur.
     *
     * @param string $mail_utilisateur Le nouveau mail de l'utilisateur.
     */
    public function setMailUtilisateur(string $mail_utilisateur): void
    {
        $this->mail_utilisateur = $mail_utilisateur;
    }

    /**
     * Modifie le mot de passe de l'utilisateur.
     *
     * @param string $mdp Le nouveau mot de passe de l'utilisateur.
     */
    public function setMdp(string $mdp): void
    {
        $this->mdp = $mdp;
    }
}