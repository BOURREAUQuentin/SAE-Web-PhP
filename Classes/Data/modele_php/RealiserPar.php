<?php

declare(strict_types=1);
namespace Data\modele_php;

/**
 * Class RealiserPar
 * Représente la relation entre un album et un artiste avec ses propriétés et méthodes associées.
 */
class RealiserPar
{
    /**
     * @var int $id_album L'identifiant unique de l'album.
     */
    private $id_album;

    /**
     * @var int $id_artiste L'identifiant unique de l'artiste.
     */
    private $id_artiste;

    /**
     * Constructeur de la classe RealiserPar.
     *
     * @param int $id_album   L'identifiant unique de l'album.
     * @param int $id_artiste L'identifiant unique de l'artiste.
     */
    public function __construct(int $id_album, int $id_artiste)
    {
        $this->id_album = $id_album;
        $this->id_artiste = $id_artiste;
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
     * Obtient l'identifiant de l'artiste.
     *
     * @return int L'identifiant de l'artiste.
     */
    public function getIdArtiste(): int
    {
        return $this->id_artiste;
    }
}