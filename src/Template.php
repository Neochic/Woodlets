<?php

namespace Neochic\Woodlets;

class Template
{
    protected $fieldTypeManager;
    protected $config;

    public function __construct($templateName, $twig, $widgetManager, $fieldTypeManager) {
        $configurator = new TemplateConfigurator($widgetManager);

        $this->template = $twig->loadTemplate($templateName);
        $this->template->renderBlock('form', array('woodlets' => $configurator));

        $this->config = $configurator->getConfig();
        $this->twig = $twig;

        $this->fieldTypeManager = $fieldTypeManager;
    }

    public function getConfig() {
        return $this->config;
    }
}
