<?php

namespace Neochic\Woodlets\Twig;

use \Twig_Extension;
use \Twig_SimpleFilter;

class I18n extends Twig_Extension
{
    protected $wpWrapper;

    public function __construct($wpWrapper) {
        $this->wpWrapper = $wpWrapper;
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('trans', [$this, 'trans']),
        ];
    }

    public function getName()
    {
        return 'woodlets_i18n_extension';
    }

    public function trans($text, $domain = 'default', $parameters = null)
    {
        return $this->wpWrapper->translate($text, $domain, $parameters);
    }
}
