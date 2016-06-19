<?php

namespace Neochic\Woodlets;

class ProfileManager
{
    protected $wpWrapper;
    protected $twig;
    protected $formManager;

    public function __construct($wpWrapper, $twig, $formManager) {
        $this->wpWrapper = $wpWrapper;
        $this->twig = $twig;
        $this->formManager = $formManager;
    }

    function form($user) {
        $profileConfigurator = new FormConfigurator();
        $this->wpWrapper->doAction('profile', $profileConfigurator);
        $template = $this->twig->loadTemplate('@woodlets/profile.twig');

        $context = array_map(function ($value) {
            return $value[0];
        }, $this->wpWrapper->getUserMeta($user->ID));

        ob_start();
        $this->formManager->form($profileConfigurator->getConfig(), $context, function($name) {
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
                    $this->wpWrapper->setUserMeta($userId, $value, $attribute);
                }
            }
        }
    }
}
