<?php

/**
 * Class Musique
 * Représente une musique avec ses propriétés et méthodes associées.
 */
class Musique
{
    /**
     * @var int $id_musique L'identifiant unique de la musique.
     */
    private $id_musique;

    /**
     * @var string $nom_musique Le nom de la musique.
     */
    private $nom_musique;

    /**
     * @var int $id_album L'identifiant unique de l'album auquel la musique est associée.
     */
    private $id_album;

    /**
     * Constructeur de la classe Musique.
     *
     * @param int    $id_musique  L'identifiant unique de la musique.
     * @param string $nom_musique Le nom de la musique.
     * @param int    $id_album    L'identifiant unique de l'album auquel la musique est associée.
     */
    public function __construct(int $id_musique, string $nom_musique, int $id_album)
    {
        $this->id_musique = $id_musique;
        $this->nom_musique = $nom_musique;
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
     * Obtient le nom de la musique.
     *
     * @return string Le nom de la musique.
     */
    public function getNomMusique(): string
    {
        return $this->nom_musique;
    }

    /**
     * Obtient l'identifiant de l'album auquel la musique est associée.
     *
     * @return int L'identifiant de l'album.
     */
    public function getIdAlbum(): int
    {
        return $this->id_album;
    }

    /**
     * Modifie le nom de la musique.
     *
     * @param string $nom_musique Le nouveau nom de la musique.
     */
    public function setNomMusique(string $nom_musique): void
    {
        $this->nom_musique = $nom_musique;
    }
}