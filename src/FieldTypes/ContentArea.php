<?php

namespace Neochic\Woodlets\FieldTypes;

class ContentArea extends FieldType
{
    protected $container;

    public function __construct($name = null, $namespace = null, $container = null) {
        call_user_func_array('parent::__construct', func_get_args());

        $this->container = $container;
    }

    protected function __createRenderContext($id, $name, $value, $field, $context, $customizer) {
        $renderContext = call_user_func_array('parent::__createRenderContext', func_get_args());
        $renderContext["widgets"] = json_decode($value, true);

        if ($renderContext["widgets"]) {
            $widgetManager = $this->container["widgetManager"];
            foreach ($renderContext["widgets"] as $key => $widget) {
                $renderContext["widgets"][$key]["widget"] = $widgetManager->getWidget($widget["widgetId"]);
            }
        }

        return $renderContext;
    }
}
