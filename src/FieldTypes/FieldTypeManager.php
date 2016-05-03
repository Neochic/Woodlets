<?php

namespace Neochic\Woodlets\FieldTypes;

class FieldTypeManager
{
    protected $fieldTypes;

    public function __construct($wpWrapper, $container) {
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
}
