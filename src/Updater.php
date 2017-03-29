<?php

namespace Neochic\Woodlets;

class Updater
{
	protected $wpWrapper;
	protected $twig;
	protected $baseName;
	protected $pluginFile;
	protected $settingsKey = 'enable_updates_notification';
	protected $checkResponse = null;

	public function __construct($twig, WordPressWrapper $wpWrapper, $baseName, $pluginFile) {
		$this->wpWrapper = $wpWrapper;
		$this->twig = $twig;
		$this->baseName = $baseName;
		$this->pluginFile = $pluginFile;
	}

	public function check() {
		$notified = $this->wpWrapper->getUserMeta($this->wpWrapper->getCurrentUserId(), 'neochic_woodlets_notice_dismissed_'.$this->settingsKey);
		if(!$notified) {
			$template = $this->twig->loadTemplate('@woodlets/autoUpdateNotice.twig');
			echo $template->render(array(
				'url' => $this->wpWrapper->escUrl( $this->wpWrapper->getAdminUrl(null, 'options-general.php?page=neochic_woodlets') ),
				'key' => $this->settingsKey
			));
		}
	}

	public function updateCheck($transient) {
		if($this->wpWrapper->getOption('neochic_woodlets_check_for_updates')) {
			if(!$this->checkResponse) {
				$url = $http_url = 'https://api.github.com/repos/Neochic/Woodlets/releases/latest';
				if ( $ssl = $this->wpWrapper->wpHttpSupports( array( 'ssl' ) ) ) {
					$url = $this->wpWrapper->setUrlScheme( $url, 'https' );
				}

				$request = $this->wpWrapper->wpRemoteGet( $url );
				if(!$this->wpWrapper->isWpError( $request )) {
					$res = json_decode( $this->wpWrapper->wpRemoteRetrieveBody( $request ) );
					$pluginData = $this->wpWrapper->getPluginData($this->pluginFile , false, false);
					$this->checkResponse = array(
						'update' => version_compare( $res->tag_name, 'v'.$pluginData['Version'], '>' ),
						'targetVersion' => $res->tag_name,
						'url' => $res->assets[0]->browser_download_url
					);
				}
			}

			if($this->checkResponse['update']) {
				$obj = new \stdClass();
				$obj->slug = $this->baseName;
				$obj->new_version = $this->checkResponse['targetVersion'];
				$obj->url = $this->checkResponse['url'];
				$obj->package = $this->checkResponse['url'];
				$transient->response[$this->baseName] = $obj;
			}
		}

		return $transient;
	}

	public function getInfo($false, $action, $arg) {
		if (isset($arg->slug) && $arg->slug === $this->baseName ) {
			$obj = new \stdClass();
			$obj->slug = $this->baseName;
			$obj->name = 'Woodlets';
			$obj->plugin_name = $this->baseName;
			$obj->sections = array(
				'description' => 'Woodlets is a WordPress plugin that makes theme development more productive and fun.'
			);
			return $obj;
		}
		return $false;
	}
}
