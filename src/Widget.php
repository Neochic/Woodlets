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

    public function __construct($id, $name, $templateName, $container, $twig, $wpWrapper, $fieldTypeManager) {
        $configurator = new WidgetConfigurator();

        $this->template = $twig->loadTemplate($templateName);
        $this->template->renderBlock('form', array('woodlet' => $configurator));

        $this->config = $configurator->getConfig();
        $this->container = $container;
        $this->twig = $twig;
        $this->wpWrapper = $wpWrapper;

        $this->fieldTypeManager = $fieldTypeManager;

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

        $instance['woodlet'] = $this->container['twigHelper'];
        echo $this->template->renderBlock('view', $instance);
    }

    public function widgetPreview($instance) {
        if(!is_array($instance)) {
            $instance = array();
        }
        return $this->template->renderBlock('preview', $instance);
    }

    public function form( $instance ) {
        $fieldTypes =  $this->fieldTypeManager->getFieldTypes();
        foreach($this->config['fields'] as $field) {

            if(!isset($fieldTypes[$field['type']])) {
                continue;
            }

            echo $fieldTypes[$field['type']]->input(
                $this->twig,
                $this->get_field_id($field['name']),
                $this->get_field_name($field['name']),
                isset($instance[$field['name']]) ? $instance[$field['name']] : null,
                $field,
                $instance);
        }
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $fieldTypes =  $this->fieldTypeManager->getFieldTypes();
        foreach($this->config['fields'] as $field) {
            if(!isset($fieldTypes[$field['type']])) {
                continue;
            }

            $instance[$field['name']] = $fieldTypes[$field['type']]->update(
                isset($new_instance[$field['name']]) ? $new_instance[$field['name']] : null,
                isset($old_instance[$field['name']]) ? $old_instance[$field['name']] : null,
                $new_instance,
                $old_instance
            );
        }
        return $instance;
    }
}