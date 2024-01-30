<?php

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
            $max_id = $stmt->fetch();
            return $max_id;
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return 0;
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

}