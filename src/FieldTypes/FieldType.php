<?php

namespace Neochic\Woodlets\FieldTypes;

use \ReflectionClass;

class FieldType implements FieldTypeInterface
{
    protected $name;

    public function __construct($name = null, $namespace = null) {
        if(!$name) {
            $reflect = new ReflectionClass($this);
            $name = lcfirst($reflect->getShortName());
        }
        $this->namespace = $namespace;
        $this->name = $name;
    }

    public function input( $twig, $id, $name, $value, $field, $context ) {
        $template = $twig->loadTemplate($this->getTemplateName());
        $renderContext = $this->__createRenderContext($id, $name, $value, $field, $context);
        return $template->render($renderContext);
    }

    public function getTemplateName() {
        $namespace = $this->namespace ? '@'.$this->namespace.'/' : '';
        return $namespace.'fieldTypes/'.$this->name.'.twig';
    }

    public function update( $newValue, $oldValue, $newContext, $oldContext ) {
        if(is_string($newValue)) {
            $newValue = trim($newValue);
        }
        return $newValue;
    }

    protected function __createRenderContext($id, $name, $value, $field, $context) {
        return array(
            'id' => $id,
            'name' => $name,
            'value' => $value,
            'field' => $field,
            'context' => $context
        );
    }
}
