<?php
declare(strict_types=1);

namespace View;

/**
 * Classe Template
 * @package View
 */
final class Template
{
    /**
     * @var string Le chemin vers le fichier de template
     */
    private string $path;

    /**
     * @var string La mise en page à utiliser pour le template
     */
    private string $layout;

    /**
     * @var string Le contenu du template
     */
    private string $content;

    /**
     * Constructeur de Template.
     * @param string $path Le chemin vers le fichier de template
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Obtient le chemin vers le fichier de template.
     *
     * @return string Le chemin vers le fichier de template
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Obtient la mise en page utilisée pour le template.
     *
     * @return string La mise en page utilisée pour le template
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * Définit la mise en page pour le template.
     *
     * @param string $layout La mise en page à définir
     * @return self
     */
    public function setLayout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Obtient le contenu du template.
     *
     * @return string Le contenu du template
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Définit le contenu pour le template.
     *
     * @param string $content Le contenu à définir
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Compile et rend le template.
     *
     * @return string Le contenu du template compilé et rendu
     */
    public function compile(): string
    {
        $content = $this->getContent();
        ob_start();
        require sprintf(
            '%s/%s.php',
            $this->getPath(),
            $this->getLayout()
        );
        return ob_get_clean();
    }
}