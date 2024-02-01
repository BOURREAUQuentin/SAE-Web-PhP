<?php

declare(strict_types=1);
namespace Data\modele_php;

/**
 * Class FairePartie
 * Représente la relation entre un album et un genre avec ses propriétés et méthodes associées.
 */
class FairePartie
{
    /**
     * @var int $id_album L'identifiant unique de l'album.
     */
    private $id_album;

    /**
     * @var int $id_genre L'identifiant unique du genre.
     */
    private $id_genre;

    /**
     * Constructeur de la classe FairePartie.
     *
     * @param int $id_album L'identifiant unique de l'album.
     * @param int $id_genre L'identifiant unique du genre.
     */
    public function __construct(int $id_album, int $id_genre)
    {
        $this->id_album = $id_album;
        $this->id_genre = $id_genre;
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
     * Obtient l'identifiant du genre.
     *
     * @return int L'identifiant du genre.
     */
    public function getIdGenre(): int
    {
        return $this->id_genre;
    }
}