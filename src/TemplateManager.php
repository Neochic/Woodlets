<?php

namespace Neochic\Woodlets;

class TemplateManager
{
    protected $twig;
    protected $wpWrapper;
    protected $fieldTypeManager;
    protected $templates;
    protected $twigHelper;
    protected $widgetManager;

    public function __construct($twig, $wpWrapper, $widgetManager, $fieldTypeManager, $twigHelper) {
        $this->twig = $twig;
        $this->wpWrapper = $wpWrapper;
        $this->widgetManager = $widgetManager;
        $this->fieldTypeManager = $fieldTypeManager;
        $this->twigHelper = $twigHelper;
        $this->templates = $this->wpWrapper->applyFilters('templates', $this->twig->getLoader()->searchTemplates('pages/*.twig'));
    }

    public function display() {
        $templateName = $this->getTemplateName();
        $template = $this->twig->loadTemplate($templateName);
        return $template->render(array('woodlet' => $this->twigHelper));
    }

    public function getTemplateName() {
        $template = $this->wpWrapper->applyFilters('default_template', '@woodlets/fallbackPageTemplate.twig');
        $data = $this->wpWrapper->getPostMeta();
        if($data && isset($data['template']) && isset($this->templates[$data['template']])) {
            $template = $data['template'];
        }

        //add main namespace to template to normalize the name
        if(strrpos($template, '@') !== 0) {
            $template = '@__main__/'.$template;
        }

        return $this->wpWrapper->applyFilters('template', $template);
    }

    public function getTemplateList() {
        return $this->templates;
    }

    public function getConfiguration() {
        $templateName = $this->getTemplateName();
        $template = new Template($templateName, $this->twig, $this->widgetManager, $this->fieldTypeManager);
        return $template->getConfig();
    }
}