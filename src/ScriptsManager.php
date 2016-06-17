<?php

namespace Neochic\Woodlets;

class ScriptsManager
{
    protected $wpWrapper;
    protected $baseurl;

    public function __construct($baseurl, $wpWrapper) {
        $this->baseurl = $baseurl;
        $this->wpWrapper = $wpWrapper;
    }

    public function addScripts() {
        $this->wpWrapper->enableThickbox();
        $this->wpWrapper->enableMediaUploader();
        $this->wpWrapper->addStyle('main-style', $this->baseurl.'/css/main.css');

        if($this->wpWrapper->applyFilters('debug', false)) {
            $this->wpWrapper->addAction( 'admin_print_footer_scripts', function() {
                /*
                 * WordPress documentation says we should not use admin_print_scripts to
                 * add scripts, but wp_register_script has no parameter for additional
                 * attributes. Therefore it's not possible to load unminified requirejs
                 * via that function.
                 * This should be executed at the very end to prevent conflicts that
                 * may occure while JS is not combined.
                 */
                echo '<script data-main="'.$this->baseurl.'/js/main" src="'.$this->baseurl.'/bower_components/requirejs/require.js"></script>';
            }, 9999999);
        } else {
            $this->wpWrapper->addScript('main-script', $this->baseurl.'/js/main-build.js');
        }
    }
}
