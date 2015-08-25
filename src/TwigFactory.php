<?php

namespace Neochic\Woodlets;

use \Twig_Environment;
use \Twig_Extension_Debug;

class TwigFactory
{

    public static function createTwig($wpWrapper, $baseDir)
    {
        /*
         * add path to views of the woodlets plugin
         */
        $paths = array(
            array(
                'path' => $baseDir . '/views/',
                'namespace' => 'woodlets'
            )
        );

        /*
         * add path to parent and child theme views
         */
        foreach ($wpWrapper->getThemePaths() as $path) {
            $path = $path . '/woodlets/';

            if(is_dir($path)) {
                array_push($paths, array(
                    'path' => $path
                ));
            }
        }

        /*
         * apply filter for additional paths (e.g. for plugins that provide widgets)
         */
        $paths = $wpWrapper->applyFilters('twig_paths', $paths);

        /*
         * create the Twig environment
         */
        $loader = new Twig\Loader();
        $twig = new Twig_Environment($loader);

        /*
         * add template paths to loader
         */
        foreach($paths as $path) {
            $namespace = $loader::MAIN_NAMESPACE;
            if(isset($path['namespace']) && $path['namespace']) {
                $namespace = $path['namespace'];
            }
            $loader->addPath($path['path'],  $namespace);
        }

        /*
         * enable debugging in the Twig environment if WordPress is in debugging mode
         */
        if($wpWrapper->isDebug()) {
            $twig->enableDebug();
            $twig->addExtension(new Twig_Extension_Debug());
        }

        return $twig;
    }
}