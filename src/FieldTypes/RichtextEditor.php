<?php

namespace Neochic\Woodlets\FieldTypes;

class RichtextEditor extends FieldType
{
    protected $defaultSettings;

    public function __construct($name = null, $namespace = null, $wpWrapper) {
        call_user_func_array('parent::__construct', func_get_args());
        $this->defaultSettings = $wpWrapper->applyFilters('rte_settings', array(
            'toolbar1' => 'bold, italic, underline, strikethrough, bullist, numlist, link, unlink, removeformat',
            'toolbar2' => ''
        ));
    }

    protected function __createRenderContext($id, $name, $value, $field, $context) {
        $renderContext = call_user_func_array('parent::__createRenderContext', func_get_args());

        $settings = $this->defaultSettings;

        if(isset($field['config']['buttons'])) {
            $settings['toolbar1'] = $field['config']['buttons'];
        }

        if(isset($field['config']['tinymce'])) {
            $settings = array_merge($settings, $field['config']['tinymce']);
        }

        $renderContext['rteSettings'] = $settings;
        return $renderContext;
    }
}