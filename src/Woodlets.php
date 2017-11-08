<?php
namespace Neochic\Woodlets;

class Woodlets
{
    protected $wpWrapper;
    public static $container = null;
	protected $theContentFilterSaveContent = null;
	protected $withinWoodletsTemplate = false;

    public function __construct($container, $wpWrapper)
    {
        $this->wpWrapper = $wpWrapper;
        self::$container = $container;
    }

    public function init()
    {
        $this->wpWrapper->addAction('neochic_woodlets_render_template', function () {
	        $this->withinWoodletsTemplate = true;
            echo self::$container['templateManager']->display();
        });

        $this->wpWrapper->addAction('plugins_loaded', function () {
            $this->wpWrapper->loadPluginTextdomain('woodlets', false, basename(self::$container["basedir"]) . "/languages");
        });

        $this->wpWrapper->addAction('widgets_init', function () {
            self::$container['widgetManager']->addWidgets();
        });

        $this->wpWrapper->addAction('customize_register', function ($wp_customize) {
            $themeCustomizer = new ThemeCustomizer($wp_customize, self::$container);
            $this->wpWrapper->doAction('theme', $themeCustomizer);
            $themeCustomizer->addControls();
        });

        $this->wpWrapper->addFilter('the_editor', function ($editor) {
            //we only do want to change main editor and keep reply editor intact
            if (strpos($editor, 'id="content"') === false) {
                return $editor;
            }

            //don't replace editor if we're not on page editing page
            if (!in_array($this->wpWrapper->pageNow() ,array("post.php", "post-new.php"))) {
                return $editor;
            }

            $editorManager = self::$container['editorManager'];
            $woodletsEditor = $editorManager->getEditor();

            if(!$woodletsEditor) {
                return $editor;
            }

            //note: escape % because wp is throwing it through printf
            return $editor.str_replace("%", "%%", $woodletsEditor);
        });

        $this->wpWrapper->addFilter('content_save_pre', function($content) {
            if (!in_array($this->wpWrapper->pageNow() ,array("post.php", "post-new.php", "revision.php"))) {
                return $content;
            }

            if (!isset($_POST['post_ID']) && !isset($_GET['revision'])) {
	            return $content;
            }

            $data = null;

            if ($this->wpWrapper->pageNow() === 'revision.php' && $_GET['action'] === 'restore') {
                $data = $this->wpWrapper->getPostMeta(null, $_GET['revision'], true);
            } else {
                $data = self::$container['editorManager']->preparePostData();
            }


            $templateManager = self::$container['templateManager'];
            $data = $this->wpWrapper->unslash($data);
            self::$container['twigHelper']->setPostMeta($data);

            $config = $templateManager->getConfiguration();
            if (!is_array($config["columns"]) || count($config["columns"]) < 1) {
                return $content;
            }

            $content = $templateManager->display(true, $data);

            /*
             * if the templates does a wp_query we've to recover the global post variable
             * wp_reset_postdata() doesn't seem to work correct while saving
             */
	        if ($this->wpWrapper->pageNow() === 'revision.php' && $_GET['action'] === 'restore') {
		        $post = $this->wpWrapper->getPost($_GET['revision']);
		        if($post && $post->post_parent > 0) {
		        	$post = $this->wpWrapper->getPost($post->post_parent);
		        }
		        $GLOBALS['post'] = $post;
	        } else {
		        $GLOBALS['post'] = $this->wpWrapper->getPost($_POST['post_ID']);
	        }

            return $content;
        });

        $this->wpWrapper->addAction('save_post', function ($postId) {
            //check nonce to prevent XSS
            if (!isset($_POST['_wpnonce']) || !$this->wpWrapper->verifyNonce($_POST['_wpnonce'], 'update-post_' . $postId)) {
                return;
            }

            //check user permission
            if (!$this->wpWrapper->isAllowed('edit_page', $postId)) {
                return;
            }

            self::$container['editorManager']->save($postId);
            self::$container['pageConfigurationManager']->save();
        });

        $this->wpWrapper->addAction('wp_restore_post_revision', function($postId, $revisionId) {
            self::$container['editorManager']->revert($revisionId);
        }, 90, 2);

        $this->wpWrapper->addAction('add_meta_boxes', function () {
            self::$container['pageConfigurationManager']->addMetaBoxes();
            self::$container['editorManager']->addMetaBox();
        });

        $this->wpWrapper->addFilter('the_content', function ($content) {
        	if ($this->withinWoodletsTemplate) {
        		return $content;
	        }

        	self::$container['twigHelper']->reloadPostMeta();
            $templateConfig = self::$container['templateManager']->getConfiguration();

	        // prevent recursive loop if the_content() is used inside the template
	        if ($this->theContentFilterSaveContent !== null) {
	        	$content = $this->theContentFilterSaveContent;
	        	$this->theContentFilterSaveContent = null;
		        return $content;
	        }

	        $this->theContentFilterSaveContent = $content;

            //if there is no column just display the whole template
            if (count($templateConfig["columns"]) < 1) {
	            $content = self::$container['templateManager']->display(true);
	            $this->theContentFilterSaveContent = null;
	            return $content;
            }

            //else display the main column
            ob_start();
	        self::$container['twigHelper']->getCol($templateConfig['settings']['mainCol']);
	        $this->theContentFilterSaveContent = null;
	        return ob_get_clean();
        }, -99999999999999999);

        $this->wpWrapper->addAction( 'show_user_profile', function($user) {
            self::$container['profileManager']->form($user);
        });
        $this->wpWrapper->addAction( 'edit_user_profile', function($user) {
            self::$container['profileManager']->form($user);
        });
        $this->wpWrapper->addAction( 'personal_options_update', function($userId) {
            self::$container['profileManager']->save($userId);
        });
        $this->wpWrapper->addAction( 'edit_user_profile_update', function($userId) {
            self::$container['profileManager']->save($userId);
        });

        $this->wpWrapper->addAction('admin_enqueue_scripts', function ($hook) {
            $isCustomize = ($hook === 'widgets.php' && $this->wpWrapper->pageNow() === 'customize.php');
            $isWidgets = ($hook === 'widgets.php' && $this->wpWrapper->pageNow() === 'widgets.php');
            $isSettingsPage = ($hook === "settings_page_neochic_woodlets");

            if (in_array($hook, array('post-new.php', 'post.php', 'profile.php', 'user-edit.php')) || $isCustomize || $isWidgets || $isSettingsPage) {
                self::$container['scriptsManager']->addScripts();
            }
        });


        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_list', function () {
            /**
             * @var \Neochic\Woodlets\WidgetManager $widgetManager;
             */
            $widgetManager = self::$container['widgetManager'];

            echo $widgetManager->getWidgetList(isset($_REQUEST['allowed']) ? $_REQUEST['allowed'] : array());
            $this->wpWrapper->wpDie();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_preview', function () {
            $instance = json_decode($this->wpWrapper->unslash($_REQUEST['instance']), true);
            $widget = $this->wpWrapper->unslash($_REQUEST['widget']);
            echo self::$container['editorManager']->getWidgetPreview($widget, $instance);
            wp_die();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_form', function () {
            $instance = isset($_REQUEST['instance']) ? $_REQUEST['instance'] : array();
            $instance = json_decode($this->wpWrapper->unslash($instance), true);
            $widgetManager = self::$container['widgetManager'];
            $widget = $widgetManager->getWidget($_REQUEST['widget']);
            if($widget) {
                $widget->form($instance);
            }
            wp_die();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_get_widget_update', function () {
            $widgetManager = self::$container['widgetManager'];
            $widgetName = $this->wpWrapper->unslash($_REQUEST['widget']);
            $widget = $widgetManager->getWidget($widgetName);

            $widgetData = $this->wpWrapper->unslash($_REQUEST['widget-' . $widget->id_base]);

            echo json_encode($widget->update(current($widgetData), array()));
            wp_die();
        });

        $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_clear_twig_cache', function () {
            $ret = TwigFactory::clearTwigCache();
            echo json_encode(array(
                "success" => $ret
            ));
            wp_die();
        });

	    $this->wpWrapper->addAction( 'admin_init', function () {
		    self::$container['settings']->init();
	    } );

	    $this->wpWrapper->addAction( 'admin_menu', function () {
		    self::$container['settings']->addPage();
	    } );

	    $this->wpWrapper->addAction( 'admin_notices', function () {
		    if (in_array($this->wpWrapper->pageNow(), array('post.php', 'post-new.php'))) {
			    self::$container['updater']->check();
		    }
	    } );

	    $this->wpWrapper->addAction('wp_ajax_neochic_woodlets_dismiss_admin_notice', function () {
		    $this->wpWrapper->setUserMeta($this->wpWrapper->getCurrentUserId(), $_REQUEST['value'], 'neochic_woodlets_notice_dismissed_'.$_REQUEST['key']);
	    	echo json_encode(true);
		    wp_die();
	    });

	    $this->wpWrapper->addFilter( 'pre_set_site_transient_update_plugins', function ($transient) {
		    return self::$container['updater']->updateCheck($transient);
	    } );

	    $this->wpWrapper->addFilter( 'plugins_api', function ($false, $action, $arg) {
		    return self::$container['updater']->getInfo($false, $action, $arg);
	    }, 10, 3);

	    $this->wpWrapper->doAction("init");
    }
}
