<?php

namespace Neochic\Woodlets;

use \Twig_Environment;
use \Twig_Extension_Debug;

class TwigFactory
{

    const TWIG_CACHE_DIR = 'wp-content/woodlets-twig-cache';

    public static function rrmdir($src) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    self::rrmdir($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        return rmdir($src);
    }

    public static function clearTwigCache() {
        $dir = ABSPATH . self::TWIG_CACHE_DIR;
        if (is_dir($dir)) {
            return self::rrmdir($dir);
        }
        return true;
    }

    public static function createTwig($wpWrapper, $baseDir, $i18n)
    {
        $paths = array();

        /*
         * add path to parent and child theme views
         */
        foreach ($wpWrapper->getThemePaths() as $path) {
            $path = $path . '/woodlets/';
	        $defaultTemplatePath = $path . '/defaultTemplates/';

            if (is_dir($path)) {
                array_push($paths, array(
                    'path' => $path
                ));
            }

            if(is_dir($defaultTemplatePath)) {
	            array_push($paths, array(
		            'path' => $defaultTemplatePath
	            ));
            }
        }

        /*
         * add path to views of the woodlets plugin as fallback
         */
        array_push($paths, array(
            'path' => $baseDir . '/views/defaultTemplates/'
        ));

        /*
         * add path to views of the woodlets plugin with explicit namespace
         */
        array_push($paths, array(
            'path' => $baseDir . '/views/',
            'namespace' => 'woodlets'
        ));

        /*
         * apply filter for additional paths (e.g. for plugins that provide widgets)
         */
        $paths = $wpWrapper->applyFilters('twig_paths', $paths);

        /*
         * create the Twig environment
         */
        $loader = new Twig\Loader();
        $cacheEnabled = $wpWrapper->getOption('neochic_woodlets_enable_twig_cache') && !$wpWrapper->isDebug();
        $twig = new Twig_Environment($loader, $cacheEnabled ? array(
            'cache' => ABSPATH . self::TWIG_CACHE_DIR,
        ) : array());

        /*
         * add template paths to loader
         */
        foreach ($paths as $path) {
            $namespace = $loader::MAIN_NAMESPACE;
            if (isset($path['namespace']) && $path['namespace']) {
                $namespace = $path['namespace'];
            }
            $loader->addPath($path['path'], $namespace);
        }

        /*
         * add i18n extension
         */

        $twig->addExtension($i18n);

        /*
         * enable debugging in the Twig environment if WordPress is in debugging mode
         */
        if ($wpWrapper->isDebug()) {
            $twig->enableDebug();
            $twig->addExtension(new Twig_Extension_Debug());
        }

        /*
         * temporary add a json_encode filter that only encodes, if it's not already json.
         * just to recover data from old versions.
         * don't rely on that functionality, since it will be removed in future releases.
         */
        $twig->addFilter(new \Twig_SimpleFilter('maybe_json_encode', function ($val) {
            return is_string($val) ? $val : json_encode($val);
        }));

        $twig->addFunction(new \Twig_SimpleFunction('defined', function ($varName) {
            return defined($varName);
        }));

	    $twig->addFilter(new \Twig_SimpleFilter('cachebust', function ($path) use ($wpWrapper) {
		    $siteUrl = $wpWrapper->siteUrl();
		    $file = $path;
		    if(substr($path, 0, strlen($siteUrl)) === $siteUrl) {
			    $file = substr($path, strlen($siteUrl));
		    }
		    $file = $wpWrapper->rootDir() . $file;
		    if(file_exists($file)) {
		    	return $path . '?' . filemtime($file);
		    }
		    return $path;
	    }));

	    $twig->addFilter(new \Twig_SimpleFilter('woodlets_date', function ($val) use ($wpWrapper) {
		    return $wpWrapper->dateI18n(null, strtotime($val));
	    }));

        $twig = $wpWrapper->applyFilters('twig', $twig);

        return $twig;
    }
}
