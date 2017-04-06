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
    protected $postTypes;
	protected $notificationKey = 'copy_default_templates';
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
        $this->postTypes = $this->wpWrapper->applyFilters('post_types', array("page" => "pages", "post" => "posts"));
        $this->templates = array();

        foreach($this->postTypes as $postType => $directory) {
            $this->templates[$postType] = $this->wpWrapper->applyFilters($postType . '_templates', $this->twig->getLoader()->searchTemplates($directory . '/*.twig'));
        }
    }

    /*
     * use data parameter to display anything else than the current post (revision or not yet saved data)
     */
    public function display($withoutParent = false, $data = null) {
        $templateName = $this->getTemplateName($this->wpWrapper->getTemplateType(), $data);
        $template = $this->twig->loadTemplate($templateName);

        $viewBlockAvailable = false;
	    $checkForView = $template;
	    while($checkForView) {
	        $checkForView = $checkForView->getParent(array());
	        if ($checkForView && $checkForView->hasBlock('view')) {
		        $viewBlockAvailable = true;
	        	break;
	        }
        }

        /*
         * If template is extending or no view/form block combination is used
         * the template should be rendered directly, else just render the view block.
         */
        if(($template->getParent(array()) && !$withoutParent) || !$viewBlockAvailable) {
            return $template->render(array('woodlets' => $this->twigHelper));
        }

        return $template->renderBlock('view', array('woodlets' => $this->twigHelper));
    }

    public function getTemplateName($type = "page", $data = null)
    {
        $template = $this->wpWrapper->applyFilters('default_template_' . $type, $this->_getDefaultTemplate($type));
		if(!$data) {
			$data = $this->wpWrapper->getPostMeta();
		}
        $postType = $this->wpWrapper->getPostType();

        if (isset($data['template']) && $postType && isset($this->templates[$postType][$data['template']])) {
            $template = $data['template'];
        }

        //add main namespace to template to normalize the name
        if (strrpos($template, '@') !== 0) {
            $template = '@__main__/' . $template;
        }

        return $this->wpWrapper->applyFilters('template', $template);
    }

    public function getPostTypes() {
        return $this->postTypes;
    }

    public function getTemplateList($type = "page") {
        return $this->templates[$type];
    }

    public function getConfiguration() {
        $templateName = $this->getTemplateName($this->wpWrapper->getTemplateType());
        $template = new Template($templateName, $this->twig, $this->widgetManager, $this->fieldTypeManager);
        return $template->getConfig();
    }

    public function checkLocalCopy() {
	    $notified = $this->wpWrapper->getUserMeta($this->wpWrapper->getCurrentUserId(), 'neochic_woodlets_notice_dismissed_'.$this->notificationKey);
	    if (!$notified) {

		    $found = false;
		    foreach ($this->wpWrapper->getThemePaths() as $path) {
			    $defaultTemplatePath = $path . '/woodlets/defaultTemplates/';
			    if (is_dir($defaultTemplatePath)) {
				    $found = true;
				    break;
			    }
		    }

		    if(!$found) {
			    $template = $this->twig->loadTemplate('@woodlets/copyDefaultTemplatesNotice.twig');
			    echo $template->render(array(
				    'url' => $this->wpWrapper->escUrl( $this->wpWrapper->getAdminUrl(null, 'options-general.php?page=neochic_woodlets') ),
				    'key' => $this->notificationKey
			    ));
		    }
	    }
    }

    protected function _getDefaultTemplate($type) {
        if (isset($this->defaultTemplates[$type])) {
            return $this->defaultTemplates[$type];
        }

        /*
         * it may be a custom post type
         */
        if(isset($this->postTypes[$type])) {
            $default = $this->twig->getLoader()->searchTemplates($this->postTypes[$type] . '/default.twig');
            if (count($default)) {
                return $this->postTypes[$type] . '/default.twig';
            }

            /*
             * if the custom template doesn't have a default template fall back to page template
             */
            return $this->defaultTemplates['page'];
        }

        /*
         * invalid type => 404
         */
        return $this->defaultTemplates['404'];
    }
}
