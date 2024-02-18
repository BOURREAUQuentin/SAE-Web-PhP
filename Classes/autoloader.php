<?php

/**
 * Class Autoloader
 */
class Autoloader{

    /**
     * Enregistre un autoloader
     */
    static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Inclue le fichier correspondant à une classe
     * @param $class string : le nom de la classe à charger
     */
    static function autoload($fqcn){
        $path = str_replace('\\', '/', $fqcn);
        require 'Classes/' . $path . '.php';
    }

}