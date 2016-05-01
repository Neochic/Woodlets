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

    public function __construct($wpWrapper, $widgetManager)
    {
        $this->widgetManager = $widgetManager;
        $this->wpWrapper = $wpWrapper;
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
        $data = $this->wpWrapper->getPostMeta();
        if ($data && $data['cols'] && isset($data['cols'][$id])) {
            foreach ($data['cols'][$id] as $widgetData) {
                $widget = $this->widgetManager->getWidget($widgetData['widgetId']);
                if($widget) {
                    $widget->widget(null, $widgetData['instance']);
                }
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
