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

        $baseId = 'neochic_woodlets_'.strtolower(str_replace('\\', '_', $id));

        $options = array();

        if(isset($this->config['settings']['description'])) {
            $options['description'] = $this->config['settings']['description'];
        }

        parent::__construct(
            $baseId,
            isset($this->config['settings']['title']) ? $this->config['settings']['title'] : $name,
            $options
        );
    }

    public function getReadableKey() {
        return $this->config['settings']['alias'] ?: $this->id_base;
    }

    public function widget( $args, $instance ) {
        if(!is_array($instance)) {
            $instance = array();
        }

        $instance['woodlets'] = $this->container['twigHelper'];
        echo $this->template->renderBlock('view', $instance);
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
