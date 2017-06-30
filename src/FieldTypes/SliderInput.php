<?php

namespace Neochic\Woodlets\FieldTypes;

class SliderInput extends FieldType
{
    protected $defaultSettings;

    public function __construct($name = null, $namespace = null, $wpWrapper)
    {
        call_user_func_array('parent::__construct', func_get_args());
        $this->defaultSettings = $wpWrapper->applyFilters('datetime_input_settings', array(
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'default' => 0
        ));
    }

    protected function __createRenderContext($id, $name, $value, $field, $context, $customizer, $useValues = null)
    {
        $renderContext = call_user_func_array('parent::__createRenderContext', func_get_args());
        $renderContext['field']['config'] = array_merge($this->defaultSettings, $renderContext['field']['config']);
        $renderContext['field']['config']['default'] = min($renderContext['field']['config']['max'], $renderContext['field']['config']['default']);
        $renderContext['field']['config']['default'] = max($renderContext['field']['config']['min'], $renderContext['field']['config']['default']);
        $renderContext['value'] = $renderContext['value'] === null ? $renderContext['field']['config']['default'] : $renderContext['value'];
        return $renderContext;
    }
}
