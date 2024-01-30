<?php

/**
 * Class AlbumPDO
 * Gère les requêtes PDO liées à la table Album.
 */
class AlbumPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe AlbumPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant maximum d'album dans la table.
     *
     * @return int L'identifiant maximum d'album.
     */
    public function getMaxIdAlbum(): int
    {
        $requete_max_id = <<<EOF
        select max(id_album) maxIdAlbum from ALBUM;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_max_id);
            $stmt->execute();
            $max_id = $stmt->fetch();
            return $max_id;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
        }
    }

    /**
     * Ajoute un nouvel album à la base de données.
     *
     * @param string $titre Le nom de l'album à ajouter.
     * @param string $annee_sortie L'année de sortie de l'album à ajouter.
     * @param int    $id_image    L'identifiant de l'image associée à l'album.
     */
    public function ajouterAlbum(string $titre, string $annee_sortie, int $id_image): void
    {
        $new_id_album = $this->getMaxIdAlbum() + 1;
        $insertion_album = <<<EOF
        insert into ALBUM (id_album, titre, annee_sortie, id_image) values (:id_album, :titre, :annee_sortie, :id_image);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_album);
            $stmt->bindParam("id_album", $new_id_album, PDO::PARAM_INT);
            $stmt->bindParam("titre", $titre, PDO::PARAM_STR);
            $stmt->bindParam("annee_sortie", $annee_sortie, PDO::PARAM_STR);
            $stmt->bindParam("id_image", $id_image, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Met à jour le nom d'un album dans la base de données.
     *
     * @param int    $id_album   L'identifiant de l'album à mettre à jour.
     * @param string $nouveau_titre  Le nouveau nom de l'album.
     */
    public function mettreAJourTitreAlbum(int $id_album, string $nouveau_titre): void
    {
        $maj_album = <<<EOF
        update ALBUM set titre = :nouveau_titre where id_album = :id_album;
        EOF;
        try{
            $stmt = $this->pdo->prepare($maj_album);
            $stmt->bindParam("titre", $nouveau_titre, PDO::PARAM_STR);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    // pas de setteur de l'année de sortie car ça n'a aucun sens
}