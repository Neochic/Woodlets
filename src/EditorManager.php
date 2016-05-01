<?php

namespace Neochic\Woodlets;

class EditorManager
{
    protected $twig;
    protected $wpWrapper;
    protected $templateManager;
    protected $widgetManager;

    public function __construct($twig, $templateManager, $widgetManager, $wpWrapper)
    {
        $this->twig = $twig;
        $this->wpWrapper = $wpWrapper;
        $this->templateManager = $templateManager;
        $this->widgetManager = $widgetManager;
    }

    public function getEditor()
    {
        $this->wpWrapper->enableThickbox();
        $template = $this->twig->loadTemplate('@woodlets/editor.twig');

        $config = $this->templateManager->getConfiguration();
        $data = $this->_getData();

        foreach ($data['cols'] as $col => $widgets) {
            foreach ($widgets as $key => $widget) {
                $data['cols'][$col][$key]['widget'] = $this->widgetManager->getWidget($widget['widgetId']);
            }
        }

        return $template->render(array(
            'config' => $config,
            'data' => $data['cols'],
            'ajaxUrl' => $this->wpWrapper->ajaxUrl()
        ));
    }

    public function addMetaBox()
    {
        $this->wpWrapper->addMetaBox('page-settings', 'Woodlets', function () {
            $template = $this->twig->loadTemplate('@woodlets/metaBox.twig');
            $data = $this->_getData();
            $postType = $this->wpWrapper->getPostType();

            echo $template->render(array(
                'templates' => $this->templateManager->getTemplateList($postType),
                'template' => $this->templateManager->getTemplateName($postType),
                'disabled' => isset($data['disabled']) ? $data['disabled'] : false
            ));
        }, ['page', 'post'], 'side', 'core');
    }

    public function save()
    {
        $post = $this->wpWrapper->getPost();

        //check nonce to prevent XSS
        if (!isset($_POST['_wpnonce']) || !$this->wpWrapper->verifyNonce($_POST['_wpnonce'], 'update-post_' . $post->ID)) {
            return;
        }

        //check user permission
        if (!$this->wpWrapper->isAllowed('edit_page', $post->ID)) {
            return;
        }

        $data = $this->_getData();

        //save woodlets page data
        if (isset($_POST["neochic_woodlets_data"])) {
            $cols = json_decode(stripslashes($_POST["neochic_woodlets_data"]), true);
            if ($cols) {
                $data['cols'] = $cols;
            }
        }

        //save woodlets settings
        if (isset($_POST["neochic_woodlet_template"])) {
            $data['template'] = $_POST["neochic_woodlet_template"];
        }

        if (isset($_POST["neochic_woodlet_disabled"])) {
            $data['disabled'] = $_POST["neochic_woodlet_disabled"];
        }

        $this->wpWrapper->setPostMeta($data);
    }

    public function getWidgetPreview($name, $instance)
    {
        $widget = $this->widgetManager->getWidget($name);
        $template = $this->twig->loadTemplate('@woodlets/widgetPreview.twig');
        return $template->render(array(
            'widgetId' => $name,
            'widget' => $widget,
            'instance' => $instance
        ));
    }

    protected function _getData()
    {
        return $this->wpWrapper->getPostMeta() ?: array('cols' => array());
    }
}
