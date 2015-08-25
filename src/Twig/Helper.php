<?php

namespace Neochic\Woodlets\Twig;

class Helper
{
    protected $loopFunctions = array(
        'title',
        'ID',
        'guid',
        'excerpt',
        'content'
    );

    protected $posts = null;
    protected $widgetManager;
    protected $wpWrapper;

    public function __construct($wpWrapper, $widgetManager) {
        $this->widgetManager = $widgetManager;
        $this->wpWrapper = $wpWrapper;
    }

    public function getPosts() {
        if($this->posts === null) {
            $this->__initPostData();
        }
        return $this->posts;
    }

    public function getSidebar($id) {
        if(is_active_sidebar($id)) {
            dynamic_sidebar( $id );
        }
    }

    public function getCol($id) {
        $data = $this->wpWrapper->getPostMeta();
        if($data && $data['cols'] && isset($data['cols'][$id])) {
            foreach($data['cols'][$id] as $widgetData) {
                $widget = $this->widgetManager->getWidget($widgetData['widgetId']);
                $widget->widget(null, $widgetData['instance']);
            }
        }
    }

    protected function __initPostData() {
        $this->posts = array();
        rewind_posts();
        while(have_posts()) {
            $post = array();
            the_post();
            foreach($this->loopFunctions as $attribute) {
                ob_start();
                call_user_func('the_'.$attribute);
                $post[$attribute] = ob_get_contents();
                ob_end_clean();
            }
            array_push($this->posts, $post);
        }
    }
}