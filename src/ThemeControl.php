<?php

namespace Neochic\Woodlets;

class ThemeControl extends \WP_Customize_Control
{
    protected $container;
    protected $config;
    protected $fieldTypeManager;

    public function __construct($manager, $id, $args = array(), $config = array(), $container = null) {
        parent::__construct($manager, $id, $args);

        $this->config = $config;
        $this->container = $container;
        $this->type = 'woodlets-'.$config['type'];
        $this->fieldTypeManager = $this->container['fieldTypeManager'];
        $fieldType = $this->fieldTypeManager->getFieldType($config['type']);
        if($fieldType) {
            $fieldType->prepare();
        }
    }

    public function render_content()
    {
        $this->fieldTypeManager->field($this->config, array($this->config['name'] => $this->value()), 'woodlets_theme_input_' . $this->config['name'], $this->config['name'], true);
    }
}
?>
