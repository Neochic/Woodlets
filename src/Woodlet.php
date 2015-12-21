<?php
namespace Neochic\Woodlets;

class Woodlet
{
    protected $wpWrapper;
    protected $container;

    public function __construct($container, $wpWrapper)
    {
        $this->wpWrapper = $wpWrapper;
        $this->container = $container;
    }

    public function init()
    {
        $this->wpWrapper->addAction('neochic_woodlets_render_template', function () {
            echo $this->container['templateManager']->display();
        });

        $this->wpWrapper->addAction('widgets_init', function () {
            $this->container['widgetManager']->addWidgets();
        });

        $this->wpWrapper->addFilter('the_editor', function ($editor) {
            //we only do want to change main editor and keep reply editor intact
            if (strpos($editor, 'id="content"') === false) {
                return $editor;
            }

            //don't replace editor if we're not on page editing page
            if (!$this->wpWrapper->isPage()) {
                return $editor;
            }

            $editorManager = $this->container['editorManager'];

            //todo: add diable functionality
            //be sure editor should be replaced and
            //woodlets is not disabled for this page

            return $editorManager->getEditor();
        });

        $this->wpWrapper->addAction('save_post', function () {
            if ($this->wpWrapper->isPage()) {
                $this->container['editorManager']->save();
            }
        });

        $this->wpWrapper->addAction('add_meta_boxes', function () {
            if ($this->wpWrapper->isPage()) {
                $this->container['editorManager']->addMetaBox();
            }
        });

        $this->wpWrapper->addFilter('the_content', function ($content) {
            //todo: add diable functionality

            if ($this->wpWrapper->isPage()) {
                return $this->container['templateManager']->display();
            }
            return $content;
        });

        $this->wpWrapper->addAction('admin_enqueue_scripts', function ($hook) {
            $post = $this->wpWrapper->getPost();
            if (in_array($hook, array('post-new.php', 'post.php')) && $post->post_type === 'page') {
                $this->container['scriptsManager']->addScripts();
            }
        });


        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_list', function () {
            /**
             * @var \Neochic\Woodlets\WidgetManager $widgetManager;
             */
            $widgetManager = $this->container['widgetManager'];

            echo $widgetManager->getWidgetList(isset($_REQUEST['allowed']) ? $_REQUEST['allowed'] : array());
            $this->wpWrapper->wpDie();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_preview', function () {
            echo $this->container['editorManager']->getWidgetPreview($_REQUEST['widget'], $_REQUEST['instance']);
            wp_die();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_form', function () {
            $instance = isset($_REQUEST['instance']) ? $_REQUEST['instance'] : array();
            $widgetManager = $this->container['widgetManager'];
            $widget = $widgetManager->getWidget($_REQUEST['widget']);
            if($widget) {
                $widget->form($instance);
            }
            wp_die();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_update', function () {
            $widgetManager = $this->container['widgetManager'];
            $widget = $widgetManager->getWidget($_REQUEST['widget']);
            echo json_encode($widget->update(current($_REQUEST['widget-' . $widget->id_base]), array()));
            wp_die();
        });
    }
}
