<?php

namespace Neochic\Woodlets;

class WidgetConfigurator extends FormConfigurator
{
    protected $config;

    public function __construct()
    {
        parent::__construct();
        $this->config['settings'] = array(
            'alias' => null
        );
    }

    public function setTitle($title)
    {
        $this->config['settings']['title'] = $title;
        return $this;
    }

    public function setDescription($description)
    {
        $this->config['settings']['description'] = $description;
        return $this;
    }

    public function setAlias($alias)
    {
        $this->config['settings']['alias'] = $alias;
        return $this;
    }
}
