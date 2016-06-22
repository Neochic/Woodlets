<?php

namespace Neochic\Woodlets;

use \WP_Widget;

class Widget extends WP_Widget implements WidgetInterface
{
    protected $template;
    protected $config;
    protected $fieldTypeManager;
    protected $twig;
    protected $wpWrapper;
    protected $container;

    public function __construct($id, $name, $templateName, $container, $twig, $wpWrapper, $formManager) {
        $configurator = new WidgetConfigurator();

        $this->template = $twig->loadTemplate($templateName);
        $this->template->renderBlock('form', array('woodlets' => $configurator));

        $this->config = $configurator->getConfig();
        $this->container = $container;
        $this->twig = $twig;
        $this->wpWrapper = $wpWrapper;

        $this->formManager = $formManager;

        $baseId = $this->config['settings']['id'] ?: 'neochic_woodlets_'.strtolower(str_replace('\\', '_', $id));

        $options = array();

        /*
         * set the number if it's an internal widget
         */
        if ($this->isInternal()) {
            $this->_set(0);
        }

        if(isset($this->config['settings']['description'])) {
            $options['description'] = $this->config['settings']['description'];
        }

        parent::__construct(
            $baseId,
            isset($this->config['settings']['title']) ? $this->config['settings']['title'] : $name,
            $options
        );
    }

    /*
     * deprecated
     * use $id_base
     */
    public function getReadableKey() {
        return $this->id_base;
    }

    public function widget( $args, $instance ) {
        if(!is_array($instance)) {
            $instance = array();
        }

        $woodletsContentArea = isset($args['woodlets_content_area']) ? $args['woodlets_content_area'] : false;

        $instance['woodlets'] = $this->container['twigHelper'];
        $instance['beforeTitle'] = $args['before_title'];
        $instance['afterTitle'] = $args['after_title'];
        $instance['woodletsContentArea'] = $woodletsContentArea;

        $wrapperTemplate = $this->twig->loadTemplate('widgetWrapper.twig');

        echo $wrapperTemplate->render(array(
            'beforeWidget' => $args['before_widget'],
            'afterWidget' => $args['after_widget'],
            'woodletsBeforeWidget' => isset($args['woodlets_before_widget']) ? $args['woodlets_before_widget'] : '',
            'woodletsAfterWidget' => isset($args['woodlets_after_widget']) ? $args['woodlets_after_widget'] : '',
            'woodletsContentArea' => $woodletsContentArea,
            'widget' => $this->template->renderBlock('view', $instance)
        ));
    }

    public function widgetPreview($instance) {
        if(!is_array($instance)) {
            $instance = array();
        }
        return $this->template->renderBlock('preview', $instance);
    }

    public function form( $instance ) {
        $this->formManager->form($this->config, $instance, function($name) {
            return array(
                'id' => $this->get_field_id($name),
                'name' => $this->get_field_name($name)
            );
        });
    }

    public function update( $new_instance, $old_instance ) {
        return $this->formManager->update($this->config, $new_instance, $old_instance);
    }

    public function isInternal() {
        return isset($this->config['settings']['register']) ? !$this->config['settings']['register'] : true;
    }
}
