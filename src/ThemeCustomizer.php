<?php

namespace Neochic\Woodlets;

class ThemeCustomizer
{
    protected $container;
    protected $manager;
    protected $forms = array();

    public function __construct($manager, $container) {
        $this->manager = $manager;
        $this->container = $container;
    }

    public function section($title, $id, $priority = 9999)
    {

        $this->manager->add_section($id, array(
            'title' => $title,
            'priority' => $priority
        ));
        
        $formConfigurator = new FormConfigurator();
        
        $this->forms[$id] = $formConfigurator;
        
        return $formConfigurator;
    }

    public function addControls() {
        foreach($this->forms as $id => $form) {
            $formConfig = $form->getConfig();

            foreach($formConfig['fields'] as $field) {
                $this->manager->add_setting($field['name'], array(
                    'sanitize_callback' => function ($value) use ($field) {
                        $fieldType = $this->container['fieldTypeManager']->getFieldType($field['type']);
                        if ($fieldType) {
                            return $fieldType->update($value, $value, array(
                                $field['name'] => $value
                            ), array(
                                $field['name'] => $value
                            ));
                        }
                        return $value;
                    }
                ));

                $this->manager->add_control(new ThemeControl($this->manager, $field['name'], array(
                    'section' => $id,
                    'settings' => $field['name']
                ), $field, $this->container));
            }
        }
    }
}
?>
