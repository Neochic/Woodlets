<?php

namespace Neochic\Woodlets\FieldTypes;

class DatetimeInput extends FieldType
{
    protected $defaultSettings;

    public function __construct($name = null, $namespace = null, $wpWrapper)
    {
        call_user_func_array('parent::__construct', func_get_args());
        $this->defaultSettings = $wpWrapper->applyFilters('datetime_input_settings', array(
            'withtime' => true,
            'withdate' => true,
            'disableNative' => true,
            'format' => array(
                "en" => array(
                    'datetime-local' => array(
                        'save' => 'YYYY[-]MM[-]DD[T]HH[:]mm',
                        'display' => 'MM[/]DD[/]YYYY hh:mm A',
                        'inputmask' => 'mm/dd/yyyy hh:mm xm'
                    ),
                    'date' => array(
                        'save' => 'YYYY[-]MM[-]DD',
                        'display' => 'MM[.]DD[.]YYYY',
                        'inputmask' => 'mm/dd/yyyy'
                    ),
                    'time' => array(
                        'save' => 'HH[:]mm',
                        'display' => 'hh[:]mm A',
                        'inputmask' => 'hh:mm t'
                    )
                ),
                "de" => array(
                    'datetime-local' => array(
                        'save' => 'YYYY[-]MM[-]DD[T]HH[:]mm',
                        'display' => 'DD[.]MM[.]YYYY[ - ]HH:mm',
                        'inputmask' => 'custom01'
                    ),
                    'date' => array(
                        'save' => 'YYYY[-]MM[-]DD',
                        'display' => 'DD[.]MM[.]YYYY',
                        'inputmask' => 'dd.mm.yyyy'
                    ),
                    'time' => array(
                        'save' => 'HH[:]mm',
                        'display' => 'HH[:]mm',
                        'inputmask' => 'hh:mm'
                    )
                )
            )
        ));
    }

    protected function __createRenderContext($id, $name, $value, $field, $context, $customizer, $useValues = null)
    {
        $renderContext = call_user_func_array('parent::__createRenderContext', func_get_args());
        $renderContext['field']['config'] = array_merge($this->defaultSettings, $renderContext['field']['config']);
        return $renderContext;
    }
}
