<?php

/**
 * Class GenrePDO
 * Gère les requêtes PDO liées à la table Genre.
 */
class GenrePDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe GenrePDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant maximum du genre dans la table.
     *
     * @return int L'identifiant maximum du genre.
     */
    public function getMaxIdGenre(): int
    {
        $requete_max_id = <<<EOF
        select max(id_genre) maxIdGenre from GENRE;
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
     * Ajoute un nouveau genre à la base de données.
     *
     * @param string $nom_genre Le nom du genre à ajouter.
     */
    public function ajouterGenre(string $nom_genre): void
    {
        $new_id_genre = $this->getMaxIdGenre() + 1;
        $insertion_genre = <<<EOF
        insert into GENRE (id_genre, nom_genre) values (:id_genre, :nom_genre);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_genre);
            $stmt->bindParam("id_genre", $new_id_genre, PDO::PARAM_INT);
            $stmt->bindParam("nom_genre", $nom_genre, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Met à jour le nom d'un genre dans la base de données.
     *
     * @param int    $id_genre   L'identifiant du genre à mettre à jour.
     * @param string $nouveau_nom  Le nouveau nom du genre.
     */
    public function mettreAJourNomGenre(int $id_genre, string $nouveau_nom): void
    {
        $maj_album = <<<EOF
        update GENRE set nom_genre = :nouveau_nom where id_genre = :id_genre;
        EOF;
        try{
            $stmt = $this->pdo->prepare($maj_album);
            $stmt->bindParam("nouveau_nom", $nouveau_nom, PDO::PARAM_STR);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }
}