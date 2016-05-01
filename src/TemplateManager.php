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
    protected $defaultTemplates = array(
        "page" => "pages/default.twig",
        "post" => "posts/default.twig",
        "attachment" => "attachment.twig",
        "404" => "404.twig",
        "category" => "category.twig",
        "tag" => "tag.twig",
        "archive" => "archive.twig",
        "search" => "search.twig",
        "list" => "list.twig"
    );

    public function __construct($twig, $wpWrapper, $widgetManager, $fieldTypeManager, $twigHelper) {
        $this->twig = $twig;
        $this->wpWrapper = $wpWrapper;
        $this->widgetManager = $widgetManager;
        $this->fieldTypeManager = $fieldTypeManager;
        $this->twigHelper = $twigHelper;
        $this->templates = array(
            "page" => $this->wpWrapper->applyFilters('page_templates', $this->twig->getLoader()->searchTemplates('pages/*.twig')),
            "post" => $this->wpWrapper->applyFilters('post_templates', $this->twig->getLoader()->searchTemplates('posts/*.twig'))
        );
    }

    public function display() {
        $templateName = $this->getTemplateName($this->wpWrapper->getTemplateType());
        $template = $this->twig->loadTemplate($templateName);

        /*
         * If template is extending or no view/form block combination is used
         * the template should be rendered directly, else just render the view block.
         */
        if($template->getParent(array()) || !$template->hasBlock('view')) {
            return $template->render(array('woodlet' => $this->twigHelper));
        }
        return $template->renderBlock('view', array('woodlet' => $this->twigHelper));
    }

    public function getTemplateName($type = "page")
    {
        if (!isset($this->defaultTemplates[$type])) {
            $type = "404";
        }
        $template = $this->wpWrapper->applyFilters('default_template_' . $type, $this->defaultTemplates[$type]);
        $data = $this->wpWrapper->getPostMeta();
        $postType = $this->wpWrapper->getPostType();

        if ($data && isset($data['template']) && $postType && isset($this->templates[$postType][$data['template']])) {
            $template = $data['template'];
        }

        //add main namespace to template to normalize the name
        if (strrpos($template, '@') !== 0) {
            $template = '@__main__/' . $template;
        }

        return $this->wpWrapper->applyFilters('template', $template);
    }

    public function getTemplateList($type = "page") {
        return $this->templates[$type];
    }

    public function getConfiguration() {
        $templateName = $this->getTemplateName();
        $template = new Template($templateName, $this->twig, $this->widgetManager, $this->fieldTypeManager);
        return $template->getConfig();
    }
}
