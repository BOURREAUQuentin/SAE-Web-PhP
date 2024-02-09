<?php

declare(strict_types=1);
namespace Modele\modele_bd;
use Modele\modele_php\Artiste;
use Modele\modele_php\Musique;
use PDO;
use PDOException;

/**
 * Class ArtistePDO
 * Gère les requêtes PDO liées à la table Artiste.
 */
class ArtistePDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe ArtistePDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant maximum d'artiste dans la table.
     *
     * @return int L'identifiant maximum d'artiste.
     */
    public function getMaxIdArtiste(): int
    {
        $requete_max_id = <<<EOF
        select max(id_artiste) maxIdArtiste from ARTISTE;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_max_id);
            $stmt->execute();
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultat["maxIdArtiste"];
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
        }
    }

    /**
     * Obtient l'artiste dans la table.
     *
     * @param int    $id_artiste   L'identifiant de l'artiste à rechercher.
     * 
     * @return Artiste L'artiste correspondant à l'identifiant donné, ou null si l'artiste n'est pas trouvée.
     */
    public function getArtisteByIdArtiste(int $id_artiste): ?Artiste
    {
        $requete_artiste = <<<EOF
        select id_artiste, nom_artiste, id_image from ARTISTE where id_artiste = :id_artiste;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_artiste);
            $stmt->bindParam("id_artiste", $id_artiste, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultat) {
                // retourne une instance de la classe Artiste avec les données récupérées
                return new Artiste($resultat['id_artiste'], $resultat['nom_artiste'], $resultat['id_image']);
            } else {
                // Aucun artiste trouvé avec l'identifiant donné
                return null;
            }
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return null;
        }
    }

    /**
     * Ajoute un nouvel artiste à la base de données.
     *
     * @param string $nom_artiste Le nom de l'artiste à ajouter.
     * @param int    $id_image    L'identifiant de l'image associée à l'artiste.
     */
    public function ajouterArtiste(string $nom_artiste, int $id_image): void
    {
        $new_id_artiste = $this->getMaxIdArtiste() + 1;
        $insertion_artiste = <<<EOF
        insert into ARTISTE (id_artiste, nom_artiste, id_image) values (:id_artiste, :nom_artiste, :id_image);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_artiste);
            $stmt->bindParam("id_artiste", $new_id_artiste, PDO::PARAM_INT);
            $stmt->bindParam("nom_artiste", $nom_artiste, PDO::PARAM_STR);
            $stmt->bindParam("id_image", $id_image, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Met à jour le nom d'un artiste dans la base de données.
     *
     * @param int    $id_artiste   L'identifiant de l'artiste à mettre à jour.
     * @param string $nouveau_nom  Le nouveau nom de l'artiste.
     */
    public function mettreAJourNomArtiste(int $id_artiste, string $nouveau_nom): void
    {
        $maj_artiste = <<<EOF
        update ARTISTE set nom_artiste = :nouveau_nom where id_artiste = :id_artiste;
        EOF;
        try{
            $stmt = $this->pdo->prepare($maj_artiste);
            $stmt->bindParam("nouveau_nom", $nouveau_nom, PDO::PARAM_STR);
            $stmt->bindParam("id_artiste", $id_artiste, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Obtient la liste des artistes d'un genre dans la table.
     * 
     * @param int $id_genre L'identifiant du genre pour lequel récupérer la liste des artistes.
     * 
     * @return array La liste des artistes d'un genre.
     */
    public function getArtistesByIdGenre(int $id_genre): array
    {
        $requete_artistes_genre = <<<EOF
        select id_artiste, nom_artiste, id_image from ARTISTE natural join APPARTENIR where id_genre = :id_genre;
        EOF;
        $les_artistes_genre = array();
        try{
            $stmt = $this->pdo->prepare($requete_artistes_genre);
            $stmt->bindParam("id_genre", $id_genre, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $artiste) {
                array_push($les_artistes_genre, new Artiste($artiste['id_artiste'], $artiste['nom_artiste'], $artiste['id_image']));
            }
            return $les_artistes_genre;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_artistes_genre;
        }
    }

    /**
     * Obtient la liste des musiques les plus streamés (limite de 5)  d'un artiste dans la table.
     * 
     * @param int $id_artiste L'identifiant de l'artiste pour lequel récupérer la liste des musiques les plus streamés (limite de 5).
     * 
     * @return array La liste des musiques les plus streamés (limite de 5) d'un artiste.
     */
    public function getMusiquesPlusStreamesByIdArtiste(int $id_artiste): array
    {
        $requete_musiques_plus_streames = <<<EOF
        select id_musique, nom_musique, duree_musique, son_musique, nb_streams, id_album from MUSIQUE natural join REALISER_PAR where id_artiste = :id_artiste ORDER BY nb_streams DESC LIMIT 5;
        EOF;
        $les_musiques_plus_streames = array();
        try{
            $stmt = $this->pdo->prepare($requete_musiques_plus_streames);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $musique) {
                array_push($les_musiques_plus_streames, new Musique($musique['id_musique'], $musique['nom_musique'], $musique['duree_musique'], $musique['son_musique'], $musique['nb_streams'], $musique['id_album']));
            }
            return $les_musiques_plus_streames;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_musiques_plus_streames;
        }
    }

    /**
     * Obtient la liste des artistes pour la recherche dans la table.
     * 
     * @param string $intitule_recherche L'intitulé de la recherche pour lequel récupérer la liste des artistes.
     * 
     * @return array La liste des artistes des résultats de la recherche.
     */
    public function getArtistesByRecherche(string $intitule_recherche): array
    {
        $requete_artistes_recherche = <<<EOF
        select id_artiste, nom_artiste, id_image from ARTISTE where nom_artiste LIKE :intitule_recherche;
        EOF;
        $les_artistes_genre = array();
        try{
            $stmt = $this->pdo->prepare($requete_artistes_recherche);
            $intitule_recherche = '%' . $intitule_recherche . '%';
            $stmt->bindParam("intitule_recherche", $intitule_recherche, PDO::PARAM_STR);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $artiste) {
                array_push($les_artistes_genre, new Artiste($artiste['id_artiste'], $artiste['nom_artiste'], $artiste['id_image']));
            }
            return $les_artistes_genre;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_artistes_genre;
        }
    }

    /**
     * Obtient la liste des artistes dans la table.
     * 
     * @return array La liste des artistes.
     */
    public function getArtistes(): array
    {
        $requete_artistes = <<<EOF
        select id_artiste, nom_artiste, id_image from ARTISTE;
        EOF;
        $les_artistes = array();
        try{
            $stmt = $this->pdo->prepare($requete_artistes);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($resultat as $artiste) {
                array_push($les_artistes, new Artiste($artiste['id_artiste'], $artiste['nom_artiste'], $artiste['id_image']));
            }
            return $les_artistes;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return $les_artistes;
        }
    }

    /**
     * Supprime un artiste de la base de données ainsi que ses musiques, albums et images associées.
     *
     * @param int $id_artiste L'identifiant de l'artiste à supprimer.
     */

    public function supprimerArtisteEtSesDependance(int $id_artiste): void
    {
        $suppresion_album_realise=<<<EOF
        delete from REALISER_PAR where id_artiste = :id_artiste1;
        EOF;
        $suppr_genre_artiste=<<<EOF
        delete from APPARTENIR where id_artiste = :id_artiste2;
        EOF;
        $suppr_image_artiste=<<<EOF
        delete from IMAGE where id_image = (select id_image from ARTISTE where id_artiste = :id_artiste3);
        EOF;
        $suppression_artiste = <<<EOF
        delete from ARTISTE where id_artiste = :id_artiste4;
        EOF;
        try{
            $stmt = $this->pdo->prepare($suppresion_album_realise);
            $stmt->bindParam("id_artiste1", $id_artiste, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $this->pdo->prepare($suppr_genre_artiste);
            $stmt->bindParam("id_artiste2", $id_artiste, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $this->pdo->prepare($suppr_image_artiste);
            $stmt->bindParam("id_artiste3", $id_artiste, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $this->pdo->prepare($suppression_artiste);
            $stmt->bindParam("id_artiste4", $id_artiste, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

}