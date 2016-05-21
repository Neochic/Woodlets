<?php

namespace Neochic\Woodlets\Twig;

class Helper
{
    protected $loopFunctions = array(
        'title',
        'ID',
        'guid',
        'excerpt',
        'content',
        'date',
        'author',
        'author_link',
        'author_posts_link'
    );

    protected $posts = null;
    protected $widgetManager;
    protected $wpWrapper;
    protected $postMeta;
    protected $pageConfig = null;
    protected $container;

    public function __construct($wpWrapper, $widgetManager, $container)
    {
        $this->widgetManager = $widgetManager;
        $this->wpWrapper = $wpWrapper;
        $this->container = $container;
        $this->postMeta = $this->wpWrapper->getPostMeta();
    }

    public function getPosts()
    {
        if ($this->posts === null) {
            $this->__initPostData();
        }
        return $this->posts;
    }

    public function getSidebar($id)
    {
        if (is_active_sidebar($id)) {
            dynamic_sidebar($id);
        }
    }

    public function getCol($id)
    {
        if ($this->postMeta['cols'] && isset($this->postMeta['cols'][$id])) {
            $this->contentArea($this->postMeta['cols'][$id]);
        }
    }

    public function getPageConfig() {
        if ($this->pageConfig) {
            return $this->pageConfig;
        }

        $data = $this->postMeta;
        $templateManager = $this->container['templateManager'];
        $templateConfig = $templateManager->getConfiguration();

        if (!isset($data['data'])) {
            $data['data'] = array();
        }

        if (!isset($data['useValues'])) {
            $data['useValues'] = array();
        }

        $fields = array();
        foreach($templateConfig['forms'] as $form) {
            $config = $form['config']->getConfig();
            foreach($config['fields'] as $field) {
                array_push($fields, $field['name']);
            }
        }

        $toInherit = array_diff($fields, $data['useValues']);
        $currentPost = $this->wpWrapper->getPost();

        foreach($toInherit as $inheritKey) {
            unset($data['data'][$inheritKey]);
        }

        while(count($toInherit) > 0 && $currentPost->post_parent) {
            $inheritData = $this->wpWrapper->getPostMeta(null, $currentPost->post_parent);
            if (!isset($inheritData['useValues'])) {
                $inheritData['useValues'] = array();
            }
            foreach($toInherit as $inheritKey) {
                if (in_array($inheritKey, $inheritData['useValues'])) {
                    if (isset($inheritData['data'][$inheritKey])) {
                        $data['data'][$inheritKey] = $inheritData['data'][$inheritKey];
                    }

                    $toInherit = array_diff($toInherit, array($inheritKey));
                }
            }
            $currentPost = $this->wpWrapper->getPost($currentPost->post_parent);
        }

        $this->pageConfig = $data['data'];

        return $this->pageConfig;
    }

    public function contentArea($widgets) {
        if (is_string($widgets)) {
            $widgets = json_decode($widgets, true);
        }

        if (!is_array($widgets)) {
            return;
        }

        foreach ($widgets as $widgetData) {
            $widget = $this->widgetManager->getWidget($widgetData['widgetId']);
            if($widget) {
                $widget->widget(null, $widgetData['instance']);
            }
        }
    }

    public function __call($name, $arguments)
    {
        if (!function_exists($name)) {
            throw new BadFunctionCallException('Call to undefined function ' . $name);
        }

        return call_user_func_array($name, $arguments);
    }

    protected function __initPostData()
    {
        $this->posts = array();
        rewind_posts();
        while (have_posts()) {
            $post = array();
            the_post();
            foreach ($this->loopFunctions as $attribute) {
                $post[$attribute] = call_user_func('get_the_' . $attribute);
            }
            $post["comments"] = get_comments();
            array_push($this->posts, $post);
        }
    }
}
