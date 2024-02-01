<?php

declare(strict_types=1);
namespace Data\modele_php;

/**
 * Class Noter
 * Représente la relation entre un utilisateur et un album avec une note, avec ses propriétés et méthodes associées.
 */
class Noter
{
    /**
     * @var int $id_album L'identifiant unique de l'album.
     */
    private $id_album;

    /**
     * @var int $id_utilisateur L'identifiant unique de l'utilisateur.
     */
    private $id_utilisateur;

    /**
     * @var int $note La note attribuée à l'album.
     */
    private $note;

    /**
     * Constructeur de la classe Noter.
     *
     * @param int $id_album       L'identifiant unique de l'album.
     * @param int $id_utilisateur L'identifiant unique de l'utilisateur.
     * @param int $note           La note attribuée à l'album.
     */
    public function __construct(int $id_album, int $id_utilisateur, int $note)
    {
        $this->id_album = $id_album;
        $this->id_utilisateur = $id_utilisateur;
        $this->note = $note;
    }

    /**
     * Obtient l'identifiant de l'album.
     *
     * @return int L'identifiant de l'album.
     */
    public function getIdAlbum(): int
    {
        return $this->id_album;
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
     * Obtient la note attribuée à l'album.
     *
     * @return int La note attribuée à l'album.
     */
    public function getNote(): int
    {
        return $this->note;
    }

    /**
     * Modifie la note attribuée à l'album.
     *
     * @param int $note La nouvelle note attribuée à l'album.
     */
    public function setNote(int $note): void
    {
        $this->note = $note;
    }
}