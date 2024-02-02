<?php

declare(strict_types=1);
namespace Modele\modele_php;

/**
 * Class Liker
 * Représente la relation entre un utilisateur et une musique qu'il a aimée, avec ses propriétés et méthodes associées.
 */
class Liker
{
    /**
     * @var int $id_musique L'identifiant unique de la musique.
     */
    private $id_musique;

    /**
     * @var int $id_utilisateur L'identifiant unique de l'utilisateur.
     */
    private $id_utilisateur;

    /**
     * Constructeur de la classe Liker.
     *
     * @param int $id_musique       L'identifiant unique de la musique.
     * @param int $id_utilisateur   L'identifiant unique de l'utilisateur.
     */
    public function __construct(int $id_musique, int $id_utilisateur)
    {
        $this->id_musique = $id_musique;
        $this->id_utilisateur = $id_utilisateur;
    }

    /**
     * Obtient l'identifiant de la musique aimée.
     *
     * @return int L'identifiant de la musique aimée.
     */
    public function getIdMusique(): int
    {
        return $this->id_musique;
    }

    /**
     * Obtient l'identifiant de l'utilisateur ayant aimé la musique.
     *
     * @return int L'identifiant de l'utilisateur ayant aimé la musique.
     */
    public function getIdUtilisateur(): int
    {
        return $this->id_utilisateur;
    }
}