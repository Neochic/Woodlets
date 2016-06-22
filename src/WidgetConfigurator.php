<?php

namespace Neochic\Woodlets;

class WidgetConfigurator extends FormConfigurator
{
    protected $config;

    public function __construct()
    {
        parent::__construct();
        $this->config['settings'] = array(
            'id' => null,
            'register' => false
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

    /*
     * deprecated
     * use setId()
     */
    public function setAlias($alias)
    {
        return $this->setId($alias);
    }

    public function setId($id)
    {
        $this->config['settings']['id'] = $id;
        return $this;
    }

    public function register()
    {
        $this->config['settings']['register'] = true;
        return $this;
    }
}
