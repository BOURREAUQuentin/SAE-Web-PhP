<?php

/**
 * Class Composer
 * Représente la relation entre une musique et un album avec ses propriétés et méthodes associées.
 */
class Composer
{
    /**
     * @var int $id_musique L'identifiant unique de la musique.
     */
    private $id_musique;

    /**
     * @var int $id_album L'identifiant unique de l'album.
     */
    private $id_album;

    /**
     * Constructeur de la classe Composer.
     *
     * @param int $id_musique L'identifiant unique de la musique.
     * @param int $id_album   L'identifiant unique de l'album.
     */
    public function __construct(int $id_musique, int $id_album)
    {
        $this->id_musique = $id_musique;
        $this->id_album = $id_album;
    }

    /**
     * Obtient l'identifiant de la musique.
     *
     * @return int L'identifiant de la musique.
     */
    public function getIdMusique(): int
    {
        return $this->id_musique;
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
}