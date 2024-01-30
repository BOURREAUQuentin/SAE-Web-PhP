<?php

/**
 * Class AppartenirPDO
 * Gère les requêtes PDO liées à la table Appartenir.
 */
class AppartenirPDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe AppartenirPDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant des genres à partir de l'identifiant d'un artiste.
     *
     * @param int $id_artiste L'identifiant de l'artiste pour lequel récupérer les identifiants des genres.
     * @return array Retourne un tableau d'identifiants de genres associés à l'artiste
     */
    public function getIdGenreByIdArtiste(int $id_artiste): array
    {
        $requete_id_genre = <<<EOF
        select id_genre from APPARTENIR where id_artiste = :id_artiste;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_genre);
            $stmt->bindParam("id_artiste", $id_artiste, PDO::PARAM_INT);
            $stmt->execute();
            $les_id_genres = $stmt->fetchAll();
            return $les_id_genres;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return [];
        }
    }

    /**
     * Obtient l'identifiant des artistes à partir de l'identifiant d'un genre.
     *
     * @param int $id_genre L'identifiant du genre pour lequel récupérer les identifiants des artistes.
     * @return array Retourne un tableau d'identifiants d'artites associés au genre
     */
    public function getIdArtisteByIdGenre(int $id_genre): array
    {
        $requete_id_artiste = <<<EOF
        select id_artiste from APPARTENIR where id_genre = :id_genre;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_id_artiste);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
            $les_id_artistes = $stmt->fetchAll();
            return $les_id_artistes;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return [];
        }
    }

    /**
     * Ajoute un nouvel appartenir à la base de données.
     *
     * @param int    $id_artiste    L'identifiant de l'artiste associé à la musique.
     * @param int    $id_genre    L'identifiant du genre associé à l'album.
     */
    public function ajouterAppartenir(int $id_artiste, int $id_genre): void
    {
        $insertion_appartenir = <<<EOF
        insert into APPARTENIR (id_artiste, id_genre) values (:id_artiste, :id_genre);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_appartenir);
            $stmt->bindParam("id_artiste", $id_artiste, PDO::PARAM_INT);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }
}