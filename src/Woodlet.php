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

        $this->wpWrapper->addAction('plugins_loaded', function () {
            $this->wpWrapper->loadPluginTextdomain('woodlets', false, $this->container["basedir"] . "/languages");
        });

        $this->wpWrapper->addAction('widgets_init', function () {
            $this->container['widgetManager']->addWidgets();
        });

        $this->wpWrapper->addAction('customize_register', function ($wp_customize) {
            $themeCustomizer = new ThemeCustomizer($wp_customize, $this->container);
            $this->wpWrapper->doAction('theme', $themeCustomizer);
            $themeCustomizer->addControls();
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

            //todo: add disable functionality
            //be sure editor should be replaced and
            //woodlets is not disabled for this page

            //note: escape % because wp is throwing it through printf
            return str_replace("%", "%%", $editorManager->getEditor());
        });

        $this->wpWrapper->addAction('save_post', function () {
            $this->container['editorManager']->save();
        });

        $this->wpWrapper->addAction('add_meta_boxes', function () {
            $this->container['editorManager']->addMetaBox();
        });

        $this->wpWrapper->addFilter('the_content', function ($content) {
            //todo: add disable functionality

            if ($this->wpWrapper->isPage()) {
                return $this->container['templateManager']->display();
            }
            return $content;
        });

        $this->wpWrapper->addAction('admin_enqueue_scripts', function ($hook) {
            $isCustomize = ($hook === 'widgets.php' && $this->wpWrapper->pageNow() === 'customize.php');
            if (in_array($hook, array('post-new.php', 'post.php')) || $isCustomize) {
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
            $instance = $this->wpWrapper->unslash($_REQUEST['instance']);
            $widget = $this->wpWrapper->unslash($_REQUEST['widget']);
            echo $this->container['editorManager']->getWidgetPreview($widget, $instance);
            wp_die();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_form', function () {
            $instance = isset($_REQUEST['instance']) ? $_REQUEST['instance'] : array();
            $instance = $this->wpWrapper->unslash($instance);
            $widgetManager = $this->container['widgetManager'];
            $widget = $widgetManager->getWidget($_REQUEST['widget']);
            if($widget) {
                $widget->form($instance);
            }
            wp_die();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_update', function () {
            $widgetManager = $this->container['widgetManager'];
            $widgetName = $this->wpWrapper->unslash($_REQUEST['widget']);
            $widget = $widgetManager->getWidget($widgetName);

            $widgetData = $this->wpWrapper->unslash($_REQUEST['widget-' . $widget->id_base]);

            echo json_encode($widget->update(current($widgetData), array()));
            wp_die();
        });
    }
}
