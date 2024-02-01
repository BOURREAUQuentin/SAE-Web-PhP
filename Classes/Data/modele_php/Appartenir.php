<?php

declare(strict_types=1);
namespace Data\modele_php;

/**
 * Class Appartenir
 * Représente la relation entre un artiste et un genre avec ses propriétés et méthodes associées.
 */
class Appartenir
{
    /**
     * @var int $id_artiste L'identifiant unique de l'artiste.
     */
    private $id_artiste;

    /**
     * @var int $id_genre L'identifiant unique du genre.
     */
    private $id_genre;

    /**
     * Constructeur de la classe Appartenir.
     *
     * @param int $id_artiste L'identifiant unique de l'artiste.
     * @param int $id_genre L'identifiant unique du genre.
     */
    public function __construct(int $id_artiste, int $id_genre)
    {
        $this->id_artiste = $id_artiste;
        $this->id_genre = $id_genre;
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