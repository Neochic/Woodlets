<?php

namespace Neochic\Woodlets\FieldTypes;

interface FieldTypeInterface
{
    public function input( $twig, $id, $name, $value, $field, $context, $customizer);
    public function update( $newValue, $oldValue, $newContext, $oldContext );
}
