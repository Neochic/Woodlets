<?php

namespace Neochic\Woodlets;

class FormConfigurator
{
    protected $config;

    public function __construct()
    {
        $this->config = array(
            'fields' => array()
        );
    }

    public function add($type, $name, $config)
    {
        array_push($this->config['fields'], array('type' => $type, 'name' => $name, 'config' => $config));
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function __toString()
    {
        return '';
    }
}
