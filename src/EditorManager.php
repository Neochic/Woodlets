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
        $template = $this->twig->loadTemplate('@woodlets/editor.twig');

        $config = $this->templateManager->getConfiguration();
        $data = $this->_getData();

        foreach ($data['cols'] as $col => $widgets) {
            foreach ($widgets as $key => $widget) {
                $data['cols'][$col][$key]['widget'] = $this->widgetManager->getWidget($widget['widgetId']);
            }
        }

        if (!is_array($config["columns"]) || count($config["columns"]) < 1) {
            return null;
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

    public function preparePostData()
    {
        $data = $this->_getData();

        //save woodlets page data
        if (isset($_POST["neochic_woodlets_data"])) {
            $cols = $this->wpWrapper->slash(json_decode(stripslashes($_POST["neochic_woodlets_data"]), true));
            if ($cols) {
                $data['cols'] = $cols;
            }
        }

        //save woodlets settings
        if (isset($_POST["neochic_woodlets_template"])) {
            $data['template'] = $_POST["neochic_woodlets_template"];
        }

        if (isset($_POST["neochic_woodlets_disabled"])) {
            $data['disabled'] = $_POST["neochic_woodlets_disabled"];
        }

        return $data;
    }

    public function save($postId)
    {
        $data = $this->preparePostData();
        $this->wpWrapper->setPostMeta($data, null, $postId);
    }

    public function revert($postId)
    {
        $revisionMeta = $this->wpWrapper->getPostMeta(null, $postId, true);
        return $this->wpWrapper->setPostMeta($revisionMeta);
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
        $data = $this->wpWrapper->getPostMeta();
        if (!isset($data['cols'])) {
            $data['cols'] = array();
        }

        return $data;
    }
}
