<?php

namespace Neochic\Woodlets;

class Settings
{
	protected $wpWrapper;
	protected $twig;

	public function __construct($twig, WordPressWrapper $wpWrapper) {
		$this->twig = $twig;
		$this->wpWrapper = $wpWrapper;
	}

	public function init() {
		$this->wpWrapper->addSettingsSection(
			'neochic_woodlets',
			'Settings for the Woodlets Plugin.',
			function($args) {},
			'neochic_woodlets'
		);

		$this->checkbox('check_for_updates', 'Check for updates', 'Enable automatic updates');
	}

	public function addPage() {
		$this->wpWrapper->addOptionsPage('Woodlets', 'Woodlets', 'manage_options', 'neochic_woodlets', function () {
			if ( ! $this->wpWrapper->CurrentUserCan( 'manage_options' ) ) {
				return;
			}

			ob_start();
			$this->wpWrapper->settingsFields( 'neochic_woodlets' );
			$this->wpWrapper->doSettingsSections( 'neochic_woodlets' );
			$this->wpWrapper->submitButton( 'Save Settings' );
			$fields = ob_get_clean();

			$template = $this->twig->loadTemplate('@woodlets/settings/settingsPage.twig');
			echo $template->render(array(
				'title' => $this->wpWrapper->getAdminPageTitle(),
				'fields' => $fields
			));
		});
	}

	protected function checkbox($id, $title, $label) {
		$id = 'neochic_woodlets_'.$id;

		$this->wpWrapper->registerSetting( 'neochic_woodlets', $id );

		$this->wpWrapper->addSettingsField(
			$id,
			$title,
			function() use ($label, $id) {
				$template = $this->twig->loadTemplate('@woodlets/settings/checkbox.twig');
				echo $template->render(array(
					'value' => $this->wpWrapper->getOption( $id ),
					'id' => $id,
					'label' => $label
				));
			},
			'neochic_woodlets',
			'neochic_woodlets'
		);
	}
}
