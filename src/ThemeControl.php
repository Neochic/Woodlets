<?php

namespace Neochic\Woodlets;

class ThemeControl extends \WP_Customize_Control
{
    protected $container;
    protected $config;

    public function __construct($manager, $id, $args = array(), $config = array(), $container = null) {
        parent::__construct($manager, $id, $args);

        $this->config = $config;
        $this->container = $container;
        $this->type = 'woodlets-'.$config['type'];
    }

    public function render_content()
    {
        $fieldTypeManager = $this->container['fieldTypeManager'];

        $fieldTypeManager->field($this->config, array($this->config['name'] => $this->value()), 'woodlets_theme_input_' . $this->config['name'], $this->config['name'], true);
    }
}
?>
