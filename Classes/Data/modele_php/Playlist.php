<?php

declare(strict_types=1);
namespace Data\modele_php;

/**
 * Class Playlist
 * Représente une playlist avec ses propriétés et méthodes associées.
 */
class Playlist
{
    /**
     * @var int $id_playlist L'identifiant unique de la playlist.
     */
    private $id_playlist;

    /**
     * @var string $nom_playlist Le nom de la playlist.
     */
    private $nom_playlist;

    /**
     * @var int $id_image L'identifiant de l'image associée à la playlist.
     */
    private $id_image;

    /**
     * @var int $id_utilisateur L'identifiant de l'utilisateur propriétaire de la playlist.
     */
    private $id_utilisateur;

    /**
     * Constructeur de la classe Playlist.
     *
     * @param int    $id_playlist     L'identifiant unique de la playlist.
     * @param string $nom_playlist    Le nom de la playlist.
     * @param int    $id_image        L'identifiant de l'image associée à la playlist.
     * @param int    $id_utilisateur  L'identifiant de l'utilisateur propriétaire de la playlist.
     */
    public function __construct(int $id_playlist, string $nom_playlist, int $id_image, int $id_utilisateur)
    {
        $this->id_playlist = $id_playlist;
        $this->nom_playlist = $nom_playlist;
        $this->id_image = $id_image;
        $this->id_utilisateur = $id_utilisateur;
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

    /**
     * Obtient le nom de la playlist.
     *
     * @return string Le nom de la playlist.
     */
    public function getNomPlaylist(): string
    {
        return $this->nom_playlist;
    }

    /**
     * Modifie le nom de la playlist.
     *
     * @param string $nom_playlist Le nouveau nom de la playlist.
     */
    public function setNomPlaylist(string $nom_playlist): void
    {
        $this->nom_playlist = $nom_playlist;
    }

    /**
     * Obtient l'identifiant de l'image associée à la playlist.
     *
     * @return int L'identifiant de l'image associée à la playlist.
     */
    public function getIdImage(): int
    {
        return $this->id_image;
    }

    /**
     * Obtient l'identifiant de l'utilisateur propriétaire de la playlist.
     *
     * @return int L'identifiant de l'utilisateur propriétaire de la playlist.
     */
    public function getIdUtilisateur(): int
    {
        return $this->id_utilisateur;
    }
}
