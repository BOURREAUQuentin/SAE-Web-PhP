<?php

/**
 * Class MusiquePDO
 * Gère les requêtes PDO liées à la table Musique.
 */
class MusiquePDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe MusiquePDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant maximum de la musique dans la table.
     *
     * @return int L'identifiant maximum de la musique.
     */
    public function getMaxIdMusique(): int
    {
        $requete_max_id = <<<EOF
        select max(id_musique) maxIdMusique from MUSIQUE;
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
     * Ajoute une nouvelle musique à la base de données.
     *
     * @param string $nom_musique Le nom de la musique à ajouter.
     * @param int $id_album L'id de l'album associé à la musique.
     */
    public function ajouterMusique(string $nom_musique, int $id_album): void
    {
        $new_id_image = $this->getMaxIdMusique() + 1;
        $insertion_image = <<<EOF
        insert into MUSIQUE (id_image, nom_musique, id_album) values (:id_image, :nom_musique, :id_album);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_image);
            $stmt->bindParam("id_image", $new_id_image, PDO::PARAM_INT);
            $stmt->bindParam("nom_musique", $nom_musique, PDO::PARAM_STR);
            $stmt->bindParam("id_album", $id_album, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Met à jour le nom d'une musique dans la base de données.
     *
     * @param int    $id_image   L'identifiant de la musique à mettre à jour.
     * @param string $nouveau_nom  Le nouveau nom de la musique.
     */
    public function mettreAJourNomMusique(int $id_image, string $nouveau_nom): void
    {
        $maj_image = <<<EOF
        update MUSIQUE set nom_musique = :nouveau_nom where id_image = :id_image;
        EOF;
        try{
            $stmt = $this->pdo->prepare($maj_image);
            $stmt->bindParam("nouveau_nom", $nouveau_nom, PDO::PARAM_STR);
            $stmt->bindParam("id_image", $id_image, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Obtient la musique dans la table.
     *
     * @param int    $id_musique   L'identifiant de la musique à rechercher.
     * 
     * @return Musique La musique correspondant à l'identifiant donné, ou null si la musique n'est pas trouvée.
     */
    public function getMusique(int $id_musique): ?Musique
    {
        $requete_musique = <<<EOF
        select id_musique, nom_musique, id_album from MUSIQUE;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_musique);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultat) {
                // retourne une instance de la classe Musique avec les données récupérées
                return new Musique($resultat['id_musique'], $resultat['nom_musique'], $resultat['id_album']);
            } else {
                // Aucune musique trouvée avec l'identifiant donné
                return null;
            }
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return null;
        }
    }
}