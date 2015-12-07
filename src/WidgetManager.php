<?php

namespace Neochic\Woodlets;

class WidgetManager
{
    protected $twig;
    protected $wpWrapper;
    protected $fieldTypeManager;
    protected $container;

    public function __construct($container, $twig, $wpWrapper, $fieldTypeManager) {
        $this->twig = $twig;
        $this->wpWrapper = $wpWrapper;
        $this->fieldTypeManager = $fieldTypeManager;
        $this->container = $container;
    }

    public function addWidgets() {
        $widgets = $this->twig->getLoader()->searchTemplates('widgets/*.twig');
        foreach($widgets as $template => $name) {
            $id = str_replace('/', '\\', substr($template, 1, -5));
            $this->wpWrapper->registerWidget('Neochic\\Woodlets\\_Widgets\\'.$id, new Widget($id, $name, $template, $this->container, $this->twig, $this->wpWrapper, $this->fieldTypeManager));
        }
    }

    public function getWidgetList($allowed = array('text')) {
        $template = $this->twig->loadTemplate('@woodlets/widgetList.twig');
        $widgets = $this->getWidgets();

        return $template->render(array(
            'widgets' => array_intersect_key($widgets, array_flip($allowed))
        ));
    }

    public function getWidgets() {
        return $this->wpWrapper->getWidgets();
    }

    public function getWidget($name) {
        $widgets = $this->getWidgets();
        if($widgets[$name]) {
            return $widgets[$name];
        }
        return null;
    }
}