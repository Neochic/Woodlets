<?php

namespace Neochic\Woodlets;

class TemplateConfigurator
{
    protected $config;
    protected $widgetManager;

    public function __construct($widgetManager)
    {
        $this->widgetManager = $widgetManager;
        $this->config = array(
            'settings' => array(),
            'columns' => array(),
            'forms' => array()
        );
    }

    public function addCol($id, $title, $config = null)
    {
        $col = array(
            'id' => $id,
            'title' => $title);

        $widgets = array_keys($this->widgetManager->getWidgets());

        $col['allowed'] = $config && isset($config['allowed']) && is_array($config['allowed']) ? $config['allowed'] : $widgets;

        if ($config && isset($config['disallowed'])) {
            $col['allowed'] = array_diff($col['allowed'], $config['disallowed']);
        }

        /*
         * By default main column is the first added column
         */
        if (!isset($this->config['settings']['mainCol'])) {
            $this->mainCol($id);
        }

        array_push($this->config['columns'], $col);
        return $this;
    }

    /*
     * Define the main column. That column is used to replace the content
     * of the_content function, which is also used for excerpts
     */
    public function mainCol($id) {
        $this->config['settings']['mainCol'] = $id;
        return $this;
    }

    public function setTitle($title)
    {
        $this->config['settings']['title'] = $title;
        return $this;
    }

    public function section($title)
    {
        $formConfigurator = new FormConfigurator();

        array_push($this->config['forms'], array(
            'title' => $title,
            'config' => $formConfigurator
        ));

        return $formConfigurator;
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
