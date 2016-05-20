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

    public function __construct($wpWrapper, $widgetManager)
    {
        $this->widgetManager = $widgetManager;
        $this->wpWrapper = $wpWrapper;
        $this->postMeta = $wpWrapper->getPostMeta();
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
        if (isset($this->postMeta['data'])) {
            return $this->postMeta['data'];
        }

        return array();
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
