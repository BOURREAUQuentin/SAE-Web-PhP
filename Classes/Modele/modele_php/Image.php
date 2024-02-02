<?php

declare(strict_types=1);
namespace Modele\modele_php;

/**
 * Class Image
 * Représente une image avec ses propriétés et méthodes associées.
 */
class Image
{
    /**
     * @var int $id_image L'identifiant unique de l'image.
     */
    private $id_image;

    /**
     * @var string $image Le chemin ou le nom de l'image.
     */
    private string $image;

    /**
     * Constructeur de la classe Image.
     *
     * @param int    $id_image L'identifiant unique de l'image.
     * @param string $image    Le chemin ou le nom de l'image.
     */
    public function __construct(int $id_image, string $image)
    {
        $this->id_image = $id_image;
        $this->image = $image;
    }

    /**
     * Obtient l'identifiant de l'image.
     *
     * @return int L'identifiant de l'image.
     */
    public function getIdImage(): int
    {
        return $this->id_image;
    }

    /**
     * Obtient le chemin ou le nom de l'image.
     *
     * @return string Le chemin ou le nom de l'image.
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Modifie le chemin ou le nom de l'image.
     *
     * @param string $image Le nouveau chemin ou nom de l'image.
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }
}