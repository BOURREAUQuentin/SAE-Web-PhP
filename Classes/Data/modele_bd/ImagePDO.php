<?php

declare(strict_types=1);
namespace Data\modele_bd;
use Data\modele_php\Image;
use PDO;
use PDOException;

/**
 * Class ImagePDO
 * Gère les requêtes PDO liées à la table Image.
 */
class ImagePDO
{
    /**
     * @var PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe ImagePDO.
     *
     * @param PDO $pdo L'objet PDO pour les interactions avec la base de données.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'identifiant maximum de l'image dans la table.
     *
     * @return int L'identifiant maximum de l'image.
     */
    public function getMaxIdImage(): int
    {
        $requete_max_id = <<<EOF
        select max(id_image) maxIdImage from IMAGE;
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
     * Obtient l'image dans la table.
     *
     * @param int    $id_image   L'identifiant de l'image à rechercher.
     * 
     * @return Image L'image correspondant à l'identifiant donné, ou null si l'image n'est pas trouvée.
     */
    public function getImageByIdImage(int $id_image): ?Image
    {
        $requete_image = <<<EOF
        select id_image, image from IMAGE where id_image = :id_image;
        EOF;
        try{
            $stmt = $this->pdo->prepare($requete_image);
            $stmt->bindParam("id_image", $id_image, PDO::PARAM_INT);
            $stmt->execute();
            // fetch le résultat sous forme de tableau associatif
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultat) {
                // retourne une instance de la classe Image avec les données récupérées
                return new Image($resultat['id_image'], $resultat['image']);
            } else {
                // Aucun image trouvé avec l'identifiant donné
                return null;
            }
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
            return null;
        }
    }

    /**
     * Ajoute une nouvelle image à la base de données.
     *
     * @param string $nom_image Le nom de l'image à ajouter.
     */
    public function ajouterImage(string $nom_image): void
    {
        $new_id_image = $this->getMaxIdImage() + 1;
        $insertion_image = <<<EOF
        insert into IMAGE (id_image, nom_image) values (:id_image, :nom_image);
        EOF;
        try{
            $stmt = $this->pdo->prepare($insertion_image);
            $stmt->bindParam("id_image", $new_id_image, PDO::PARAM_INT);
            $stmt->bindParam("nom_image", $nom_image, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * Met à jour le nom d'une image dans la base de données.
     *
     * @param int    $id_image   L'identifiant de l'image à mettre à jour.
     * @param string $nouveau_nom  Le nouveau nom de l'image.
     */
    public function mettreAJourNomImage(int $id_image, string $nouveau_nom): void
    {
        $maj_image = <<<EOF
        update IMAGE set nom_image = :nouveau_nom where id_image = :id_image;
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
}