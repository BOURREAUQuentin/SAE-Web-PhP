<?php

/**
 * Class Album
 * Représente un album avec ses propriétés et méthodes associées.
 */
class Album
{
    /**
     * @var int $id_album L'identifiant unique de l'album.
     */
    private $id_album;

    /**
     * @var string $titre Le titre de l'album.
     */
    private $titre;

    /**
     * @var int $annee_sortie L'année de sortie de l'album.
     */
    private $annee_sortie;

    /**
     * @var int $id_image L'identifiant de l'image associée à l'album.
     */
    private $id_image;

    /**
     * Constructeur de la classe Album.
     *
     * @param int    $id_album    L'identifiant unique de l'album.
     * @param string $titre       Le titre de l'album.
     * @param int    $annee_sortie L'année de sortie de l'album.
     * @param int    $id_image    L'identifiant de l'image associée à l'album.
     */
    public function __construct(int $id_album, string $titre, int $annee_sortie, int $id_image)
    {
        $this->id_album = $id_album;
        $this->titre = $titre;
        $this->annee_sortie = $annee_sortie;
        $this->id_image = $id_image;
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
     * Obtient le titre de l'album.
     *
     * @return string Le titre de l'album.
     */
    public function getTitre(): string
    {
        return $this->titre;
    }

    /**
     * Obtient l'année de sortie de l'album.
     *
     * @return int L'année de sortie de l'album.
     */
    public function getAnneeSortie(): int
    {
        return $this->annee_sortie;
    }

    /**
     * Obtient l'identifiant de l'image associée à l'album.
     *
     * @return int L'identifiant de l'image.
     */
    public function getIdImage(): int
    {
        return $this->id_image;
    }

    /**
     * Modifie le titre de l'album.
     *
     * @param string $titre Le nouveau titre de l'album.
     */
    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    /**
     * Modifie l'année de sortie de l'album.
     *
     * @param int $annee_sortie La nouvelle année de sortie de l'album.
     */
    public function setAnneeSortie(int $annee_sortie): void
    {
        $this->annee_sortie = $annee_sortie;
    }
}