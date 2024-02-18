<?php

declare(strict_types=1);
namespace Modele\modele_php;

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
     * @var string $duree_musique La durée de la musique.
     */
    private $duree_musique;

    /**
     * @var string $son_musique Le son de la musique.
     */
    private $son_musique;

    /**
     * @var int $nb_streams Le nombre de streams de la musique.
     */
    private $nb_streams;

    /**
     * @var int $id_album L'identifiant unique de l'album auquel la musique est associée.
     */
    private $id_album;

    /**
     * Constructeur de la classe Musique.
     *
     * @param int    $id_musique  L'identifiant unique de la musique.
     * @param string $nom_musique Le nom de la musique.
     * @param string $duree_musique La durée de la musique.
     * @param string $son_musique Le son de la musique (lien pour lire la musique).
     * @param int    $nb_streams    Le nombre de streams (d'écoutes) de la musique.
     * @param int    $id_album    L'identifiant unique de l'album auquel la musique est associée.
     */
    public function __construct(int $id_musique, string $nom_musique, string $duree_musique, string $son_musique, int $nb_streams, int $id_album)
    {
        $this->id_musique = $id_musique;
        $this->nom_musique = $nom_musique;
        $this->duree_musique = $duree_musique;
        $this->son_musique = $son_musique;
        $this->nb_streams = $nb_streams;
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
     * Obtient la durée de la musique.
     *
     * @return string La durée de la musique.
     */
    public function getDureeMusique(): string
    {
        return $this->duree_musique;
    }

    /**
     * Obtient le son de la musique.
     *
     * @return string Le son de la musique.
     */
    public function getSonMusique(): string
    {
        return $this->son_musique;
    }

    /**
     * Obtient le nombre de streams de la musique.
     *
     * @return int Le nombre de streams de la musique.
     */
    public function getNbStreams(): int
    {
        return $this->nb_streams;
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
}