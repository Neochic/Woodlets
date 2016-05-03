<?php

namespace Neochic\Woodlets\FieldTypes;
use Neochic\Woodlets\Twig\FieldTypeMediaHelper;

class Media extends FieldType
{
    protected $defaultSettings;
    protected $wpWrapper;

    public function __construct($name = null, $namespace = null, $wpWrapper) {
        call_user_func_array('parent::__construct', func_get_args());
        $this->wpWrapper = $wpWrapper;

        $this->defaultSettings = $wpWrapper->applyFilters('media_settings', array(
            "title" => 'Select or upload media',
            "library" => array(
                "type"=> '*'
            ),
            "button" => array(
                "text" => 'Use this media'
            ),
            "multiple" => false
        ));
    }

    protected function __createRenderContext($id, $name, $value, $field, $context) {
        $renderContext = call_user_func_array('parent::__createRenderContext', func_get_args());

        $settings = $this->defaultSettings;

        if(isset($field['config']['type'])) {
            $settings['library']['type'] = $field['config']['type'];
        }

        if(isset($field['config']['wpMedia'])) {
            $settings = array_merge($settings, $field['config']['wpMedia']);
        }

        $renderContext['mediaSettings'] = $settings;

        $renderContext['value'] = $this->wpWrapper->getAttachment($renderContext['value']);

        return $renderContext;
    }
}
