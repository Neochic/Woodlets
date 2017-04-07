<?php

namespace Neochic\Woodlets;

class Updater
{
	protected $wpWrapper;
	protected $twig;
	protected $baseName;
	protected $pluginFile;
	protected $settingsKey = 'enable_updates_notification';
	protected $settingsMajorReleaseKey = 'major_release';
	protected $checkResponse = null;

	public function __construct($twig, WordPressWrapper $wpWrapper, $baseName, $pluginFile) {
		$this->wpWrapper = $wpWrapper;
		$this->twig = $twig;
		$this->baseName = $baseName;
		$this->pluginFile = $pluginFile;
	}

	public function check() {
		$activated = $this->wpWrapper->getOption('neochic_woodlets_check_for_updates');
		$notified = $this->wpWrapper->getUserMeta($this->wpWrapper->getCurrentUserId(), 'neochic_woodlets_notice_dismissed_'.$this->settingsKey);
		if(!$notified && !$activated) {
			$template = $this->twig->loadTemplate('@woodlets/autoUpdateNotice.twig');
			echo $template->render(array(
				'url' => $this->wpWrapper->escUrl( $this->wpWrapper->getAdminUrl(null, 'options-general.php?page=neochic_woodlets') ),
				'key' => $this->settingsKey
			));
		}

		$newMajorRelease = $this->wpWrapper->getOption('neochic_woodlets_new_major_release');
		$notified = $this->wpWrapper->getUserMeta($this->wpWrapper->getCurrentUserId(), 'neochic_woodlets_notice_dismissed_'.$this->settingsMajorReleaseKey);
		if($newMajorRelease && $newMajorRelease[0] !== $notified) {
			$template = $this->twig->loadTemplate('@woodlets/majorReleaseNotice.twig');
			echo $template->render(array(
				'key' => $this->settingsMajorReleaseKey,
				'value' => $newMajorRelease[0]
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
					$versionExpression = '/^v?(\d+)\.(\d+)\.(\d+)$/';
					$res = json_decode( $this->wpWrapper->wpRemoteRetrieveBody( $request ) );
					$pluginData = $this->wpWrapper->getPluginData($this->pluginFile , false, false);

					preg_match($versionExpression, $res->tag_name, $targetVersion);
					preg_match($versionExpression, $pluginData['Version'], $currentVersion);

					$update = false;
					if(count($targetVersion) === 4 && count($currentVersion) === 4) {
						//only update to versions of the same major release
						if(intval($targetVersion[1]) === intval($currentVersion[1])) {
							if(intval($targetVersion[2]) > intval($currentVersion[2])) {
								$update = true;
							}

							if(intval($targetVersion[2]) === intval($currentVersion[2]) && intval($targetVersion[3]) > intval($currentVersion[3])) {
								$update = true;
							}
						}

						//do update from 0.x.x versions to 1.x.x
						if(intval($targetVersion[1]) === 1 && intval($currentVersion[1]) === 0) {
							$update = true;
						} else {
							//remember that there is a new major release to show admin notification
							if(intval($targetVersion[1]) > intval($currentVersion[1])) {
								$this->wpWrapper->updateOption('neochic_woodlets_new_major_release', array($pluginData['Version'], $res->tag_name));
							}
						}
					}

					$this->checkResponse = array(
						'update' => $update,
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
