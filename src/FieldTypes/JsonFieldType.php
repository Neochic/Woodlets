<?php

namespace Neochic\Woodlets\FieldTypes;

class JsonFieldType extends FieldType
{
    public function update( $newValue, $oldValue, $newContext, $oldContext ) {
        $newValue = call_user_func_array('parent::update', func_get_args());
        return json_decode($newValue, true);
    }

    public function getJsValue($value) {
        return json_encode($value);
    }
}
