<?php

namespace Neochic\Woodlets;

class WidgetConfigurator
{
    protected $config;

    public function __construct() {
        $this->config = array(
            'settings' => array(
                'alias' => null
            ),
            'fields' => array()
        );
    }

    public function add($type, $name ,$config) {
        array_push($this->config['fields'], array('type' => $type, 'name' => $name, 'config' => $config));
        return $this;
    }

    public function setTitle($title) {
        $this->config['settings']['title'] = $title;
        return $this;
    }

    public function setDescription($description) {
        $this->config['settings']['description'] = $description;
        return $this;
    }

    public function setAlias($alias) {
        $this->config['settings']['alias'] = $alias;
        return $this;
    }

    public function getConfig() {
        return $this->config;
    }

    public function __toString()
    {
        return '';
    }
}