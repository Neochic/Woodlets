<?php

namespace Neochic\Woodlets;

use Neochic\Woodlets\FieldTypes\FieldTypeManager;
use \Twig_Environment;

class FormManager
{
    protected $fieldTypeManager;
    protected $twig;

    public function __construct(FieldTypeManager $fieldTypeManager, Twig_Environment $twig) {
        $this->fieldTypeManager = $fieldTypeManager;
        $this->twig = $twig;
    }

    public function form($config = array(), $instance = array(), $getFieldAttributes, $isThemeConfig = false, $useValues = null) {
        foreach($config['fields'] as $field) {
            /*
             * todo: we should definitely use a more OO way
             * to get the name and id for the fields
             * maybe this class should be a trait
             */
            $fieldAttributes = $getFieldAttributes($field['name']);
            $fieldType = $this->fieldTypeManager->getFieldType($field['type']);
            if($fieldType) {
                $fieldType->prepare();
            }
            $this->fieldTypeManager->field($field, $instance, $fieldAttributes['id'], $fieldAttributes['name'], $isThemeConfig, $useValues);
        }
    }
    
    public function update($config = array(), $new_instance = array(), $old_instance = array()) {
        $instance = array();
        $fieldTypes =  $this->fieldTypeManager->getFieldTypes();
        foreach($config['fields'] as $field) {
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
