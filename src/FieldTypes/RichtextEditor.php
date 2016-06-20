<?php

namespace Neochic\Woodlets\FieldTypes;

use \_WP_Editors;

class RichtextEditor extends FieldType
{
    protected $defaultSettings;

    public function __construct($name = null, $namespace = null, $wpWrapper)
    {
        call_user_func_array('parent::__construct', func_get_args());
        $this->defaultSettings = $wpWrapper->applyFilters('rte_settings', array(
            'toolbar1' => 'bold, italic, underline, strikethrough, bullist, numlist, link, unlink, removeformat',
            'toolbar2' => ''
        ));
    }

    protected function __loadTinyMce()
    {
        if (!class_exists('_WP_Editors', false)) {
            require_once(ABSPATH . WPINC . '/class-wp-editor.php');
        }

        /*
         * _WP_Editors shouldn't be used directly, but in this case
         * it's the best way to load WordPress RTE stuff and handle
         * RTE editors in Javascript
         */
        $set = \_WP_Editors::parse_settings(null, array(
            'tinymce' => true,
            'quicktags' => false
        ));

        \_WP_Editors::editor_settings('woodlets_tiny_mce', $set);
    }

    public function prepare() {
        $this->__loadTinyMce();
        return call_user_func_array('parent::prepare', func_get_args());
    }

    public function input( $twig, $id, $name, $value, $field, $context, $customizer = false, $useValues = null) {
        /*
         * load tinymce scripts via WordPress
         */

        return call_user_func_array('parent::input', func_get_args());
    }

    protected function __createRenderContext($id, $name, $value, $field, $context, $customizer, $useValues = null)
    {
        $renderContext = call_user_func_array('parent::__createRenderContext', func_get_args());

        $settings = $this->defaultSettings;

        if (isset($field['config']['buttons'])) {
            $settings['toolbar1'] = $field['config']['buttons'];
        }

        if (isset($field['config']['tinymce'])) {
            $settings = array_merge($settings, $field['config']['tinymce']);
        }

        $renderContext['rteSettings'] = $settings;
        return $renderContext;
    }
}
