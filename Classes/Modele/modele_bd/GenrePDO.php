<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\Genre;
use PDO;
use PDOException;

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
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultat["maxIdGenre"];
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
        }
    }

    /**
     * Obtient le genre dans la table.
     *
     * @param int    $id_genre   L'identifiant du genre à rechercher.
     * 
     * @return Genre Le genre correspondant à l'identifiant donné, ou null si l'genre n'est pas trouvée.
     */
    public function getGenreByIdGenre(int $id_genre): ?Genre
    {
        $requete_genre = <<<EOF
        select id_genre, nom_genre from GENRE where id_genre = :id_genre;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_genre);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultat) {
                // retourne une instance de la classe Genre avec les données récupérées
                return new Genre($resultat['id_genre'], $resultat['nom_genre']);
            } else {
                // Aucun genre trouvé avec l'identifiant donné
                return null;
            }
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return null;
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
        $maj_genre = <<<EOF
        update GENRE set nom_genre = :nouveau_nom where id_genre = :id_genre;
        EOF;
        try{
            $stmt = $this->pdo->prepare($maj_genre);
            $stmt->bindParam("nouveau_nom", $nouveau_nom, PDO::PARAM_STR);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Obtient la liste des genres dans la table.
     * 
     * @return array La liste des genres.
     */
    public function getGenres(): array
    {
        $requete_genres = <<<EOF
        select id_genre, nom_genre from GENRE;
        EOF;
        $les_genres = array();
        try{
            $stmt = $this->pdo->prepare($requete_genres);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $genre) {
                array_push($les_genres, new Genre($genre['id_genre'], $genre['nom_genre']));
            }
            return $les_genres;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_genres;
        }
    }

    /**
     * Supprime le genre associé à l'id genre dans la table.
     * 
     * @param int $id_genre L'identifiant du genre pour lequel supprimer le genre.
     */
    public function supprimerGenreByIdGenre(int $id_genre): void
    {
        $requete_suppression_genre = <<<EOF
        delete from GENRE where id_genre = :id_genre;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_suppression_genre);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Obtient le genre dans la table.
     *
     * @param string    $nom_genre   Le nom du genre à rechercher.
     * 
     * @return Genre Le genre correspondant au nom du genre donné, ou null si le genre n'est pas trouvé.
     */
    public function getGenreByNomGenre(string $nom_genre): ?Genre
    {
        $requete_genre = <<<EOF
        select id_genre, nom_genre from GENRE where nom_genre = :nom_genre;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_genre);
            $stmt->bindParam("nom_genre", $nom_genre, PDO::PARAM_STR);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultat) {
                // retourne une instance de la classe Genre avec les données récupérées
                return new Genre($resultat['id_genre'], $resultat['nom_genre']);
            } else {
                // Aucun Genre trouvé avec l'identifiant donné
                return null;
            }
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return null;
        }
    }
}