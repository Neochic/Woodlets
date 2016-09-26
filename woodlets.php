<?php
/*
Plugin Name: Woodlets
Description: Create WordPress Widgets using Twig Templates
Author: Christoph Stickel <christoph@neochic.de>
Version: 0.2.3
Author URI: http://www.neochic.de/
Text Domain: Neochic\Woodlets
*/

namespace Neochic\Woodlets;

/*
 * don't do anything if not called from WordPress
 * https://codex.wordpress.org/Writing_a_Plugin
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * wrapped to keep global namespace clean
 */

call_user_func(function () {

    /*
     * include composer autoloader if packaged version is used
     * if installed via composer autoloader should already be added
     */
    $autoloader = __DIR__ . '/vendor/autoload.php';
    if (is_file($autoloader)) {
        require_once($autoloader);
    }

    /*
     * initialize woodlets
     */
    $container = ContainerFactory::createContainer();
    $container['autoloader'] = $autoloader;
    $container['basedir'] = __DIR__;
    $container['baseurl'] = plugins_url('', __FILE__);
    $woodlets = $container['woodlets'];
    $woodlets->init();
});
