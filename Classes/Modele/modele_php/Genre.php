<?php

declare(strict_types=1);
namespace Modele\modele_php;

/**
 * Class Genre
 * Représente un genre musical avec ses propriétés et méthodes associées.
 */
class Genre
{
    /**
     * @var int $id_genre L'identifiant unique du genre musical.
     */
    private $id_genre;

    /**
     * @var string $nom_genre Le nom du genre musical.
     */
    private $nom_genre;

    /**
     * Constructeur de la classe Genre.
     *
     * @param int    $id_genre  L'identifiant unique du genre musical.
     * @param string $nom_genre Le nom du genre musical.
     */
    public function __construct(int $id_genre, string $nom_genre)
    {
        $this->id_genre = $id_genre;
        $this->nom_genre = $nom_genre;
    }

    /**
     * Obtient l'identifiant du genre musical.
     *
     * @return int L'identifiant du genre musical.
     */
    public function getIdGenre(): int
    {
        return $this->id_genre;
    }

    /**
     * Obtient le nom du genre musical.
     *
     * @return string Le nom du genre musical.
     */
    public function getNomGenre(): string
    {
        return $this->nom_genre;
    }
}