<?php

namespace Neochic\Woodlets;

class ProfileManager
{
    protected $wpWrapper;
    protected $twig;
    protected $formManager;
    protected $fieldTypeManager;

    public function __construct($wpWrapper, $twig, $fieldTypeManager, $formManager) {
        $this->wpWrapper = $wpWrapper;
        $this->twig = $twig;
        $this->formManager = $formManager;
        $this->fieldTypeManager = $fieldTypeManager;
    }

    function form($user) {
        $template = $this->twig->loadTemplate('@woodlets/profile.twig');

        $context = array_map(function ($value) {
            return $this->wpWrapper->mayBeUnserialize($value[0]);
        }, $this->wpWrapper->getUserMeta($user->ID));

        ob_start();
        $this->formManager->form($this->_getConfig(), $context, function($name) {
            return array(
                "id" => 'woodlets_profile_' . $name,
                "name" => 'woodlets_profile['. $name . ']'
            );
        });
        $fields = ob_get_clean();

        echo $template->render(array(
            'fields' => $fields
        ));
    }

    function save($userId) {
        if ( current_user_can('edit_user',$userId) ) {
            if (isset($_REQUEST['woodlets_profile']) && is_array($_REQUEST['woodlets_profile'])) {
                foreach($_REQUEST['woodlets_profile'] as $attribute => $value) {
                    $fieldConfig = current(array_filter(
                        $this->_getConfig()['fields'],
                        function ($field) use ($attribute) {
                            return $field["name"] === $attribute;
                        }
                    ));

                    if ($fieldConfig) {
                        $value = $this->wpWrapper->unslash($value);
                        $value = $this->fieldTypeManager->getFieldType($fieldConfig["type"])->update($value, $value, array(
                            $attribute => $value
                        ), array(
                            $attribute => $value
                        ));
                    }

                    $this->wpWrapper->setUserMeta($userId, $value, $attribute);
                }
            }
        }
    }

    protected function _getConfig() {
        $profileConfigurator = new FormConfigurator();
        $this->wpWrapper->doAction('profile', $profileConfigurator);
        return $profileConfigurator->getConfig();
    }
}
