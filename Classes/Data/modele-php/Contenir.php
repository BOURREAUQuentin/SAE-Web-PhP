<?php

/**
 * Class Contenir
 * Représente la relation entre une musique et une playlist avec ses propriétés et méthodes associées.
 */
class Contenir
{
    /**
     * @var int $id_musique L'identifiant unique de la musique.
     */
    private $id_musique;

    /**
     * @var int $id_playlist L'identifiant unique de la playlist.
     */
    private $id_playlist;

    /**
     * Constructeur de la classe Contenir.
     *
     * @param int $id_musique   L'identifiant unique de la musique.
     * @param int $id_playlist  L'identifiant unique de la playlist.
     */
    public function __construct(int $id_musique, int $id_playlist)
    {
        $this->id_musique = $id_musique;
        $this->id_playlist = $id_playlist;
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
     * Obtient l'identifiant de la playlist.
     *
     * @return int L'identifiant de la playlist.
     */
    public function getIdPlaylist(): int
    {
        return $this->id_playlist;
    }
}