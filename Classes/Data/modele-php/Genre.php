<?php

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
     * @var int $id_image L'identifiant de l'image associée au genre.
     */
    private $id_image;

    /**
     * Constructeur de la classe Genre.
     *
     * @param int    $id_genre  L'identifiant unique du genre musical.
     * @param string $nom_genre Le nom du genre musical.
     * @param int    $id_image  L'identifiant de l'image associée au genre.
     */
    public function __construct(int $id_genre, string $nom_genre, int $id_image)
    {
        $this->id_genre = $id_genre;
        $this->nom_genre = $nom_genre;
        $this->id_image = $id_image;
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

    /**
     * Obtient l'identifiant de l'image associée au genre.
     *
     * @return int L'identifiant de l'image associée au genre.
     */
    public function getIdImage(): int
    {
        return $this->id_image;
    }

    /**
     * Modifie le nom du genre musical.
     *
     * @param string $nom_genre Le nouveau nom du genre musical.
     */
    public function setNomGenre(string $nom_genre): void
    {
        $this->nom_genre = $nom_genre;
    }
}