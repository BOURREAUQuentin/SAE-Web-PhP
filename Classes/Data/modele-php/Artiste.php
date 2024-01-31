<?php

/**
 * Class Artiste
 * Représente un artiste avec ses propriétés et méthodes associées.
 */
class Artiste
{
    /**
     * @var int $id_artiste L'identifiant unique de l'artiste.
     */
    private $id_artiste;

    /**
     * @var string $nom_artiste Le nom de l'artiste.
     */
    private $nom_artiste;

    /**
     * @var int $id_image L'identifiant de l'image associée à l'artiste.
     */
    private $id_image;

    /**
     * Constructeur de la classe Artiste.
     *
     * @param int    $id_artiste  L'identifiant unique de l'artiste.
     * @param string $nom_artiste Le nom de l'artiste.
     * @param int    $id_image    L'identifiant de l'image associée à l'artiste.
     */
    public function __construct(int $id_artiste, string $nom_artiste, int $id_image)
    {
        $this->id_artiste = $id_artiste;
        $this->nom_artiste = $nom_artiste;
        $this->id_image = $id_image;
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
     * Obtient le nom de l'artiste.
     *
     * @return string Le nom de l'artiste.
     */
    public function getNomArtiste(): string
    {
        return $this->nom_artiste;
    }

    /**
     * Modifie le nom de l'artiste.
     *
     * @param string $nom_artiste Le nouveau nom de l'artiste.
     */
    public function setNomArtiste(string $nom_artiste): void
    {
        $this->nom_artiste = $nom_artiste;
    }

    /**
     * Obtient l'identifiant de l'image associée à l'artiste.
     *
     * @return int L'identifiant de l'image.
     */
    public function getIdImage(): int
    {
        return $this->id_image;
    }

    /**
     * Modifie l'identifiant de l'image associée à l'artiste.
     *
     * @param int $id_image Le nouvel identifiant de l'image.
     */
    public function setIdImage(int $id_image): void
    {
        $this->id_image = $id_image;
    }
}