<?php

namespace Neochic\Woodlets\FieldTypes;

class FieldTypeManager
{
    protected $fieldTypes;
    protected $twig;

    public function __construct($wpWrapper, $twig, $container) {
        $this->twig = $twig;

        $fieldTypes = array(
            'text' => new FieldType('text', 'woodlets'),
            'textarea' => new FieldType('textarea', 'woodlets'),
            'select' => new FieldType('select', 'woodlets'),
            'radio' => new FieldType('radio', 'woodlets'),
            'checkbox' => new FieldType('checkbox', 'woodlets'),
            'contentArea' => new ContentArea('contentArea', 'woodlets', $container),
            'rte' => new RichtextEditor('rte', 'woodlets', $wpWrapper),
            'media' => new Media('media', 'woodlets', $wpWrapper)
        );

        $this->fieldTypes = $wpWrapper->applyFilters('field_types', $fieldTypes);
    }

    public function getFieldTypes() {
        return $this->fieldTypes;
    }

    public function field($field, $context, $id, $name, $isThemeConfig = false) {
        if(!isset($this->fieldTypes[$field['type']])) {
            return;
        }

        echo $this->fieldTypes[$field['type']]->input(
            $this->twig,
            $id,
            $name,
            isset($context[$field['name']]) ? $context[$field['name']] : null,
            $field,
            $context,
            $isThemeConfig);
    }
}
