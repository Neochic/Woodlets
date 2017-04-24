# Woodlets 
[![Build Status](https://travis-ci.org/Neochic/Woodlets.svg)](https://travis-ci.org/Neochic/Woodlets)
[![Join the chat at https://gitter.im/Woodlets/Lobby](https://badges.gitter.im/Woodlets/Lobby.svg)](https://gitter.im/Woodlets/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)


**Woodlets is beta. We think it's stable enough be used it in productive environments and we do so.  
However you should copy the default templates directory from views/defaultTemplates into the woodlets directory of your theme. This helps to prevent incompatibilities with future Woodlets versions.**  

**Feedback and feature requests are welcome!**

Woodlets is a WordPress plugin that makes theme development more productive and fun.  
The main features are:
* Heavily Twig based theme development
* Create widgets with a single Twig-Template file
* Multi column page layouts
* Custom page template fields with data inheritance
* Simple way to add controls to the theme customizer
* Compatible with native WordPress widgets

## Installation
Installation via [TGM Plugin Activation](http://tgmpluginactivation.com/) is recommended. With TGMPA Plugin Activation you can define Woodlets as a required dependency for your theme.  
But there is also a bundled version that can be installed manually.
### Install via TGM Plugin Activation
1. Install TGM Plugin Activation as they explain in their [installation manual](http://tgmpluginactivation.com/installation/).
2. Include and configure the TGM Plugin Activation library to load Woodlets:

    ```php
    <?php
    require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
    
    add_action( 'tgmpa_register', function() {
        $plugins = array(
            array(
                'name'               => 'Woodlets',
                'slug'               => 'woodlets',
                'source'             => 'https://github.com/Neochic/Woodlets/releases/download/v0.5.8/woodlets-v0.5.8-bundled.zip',
                'required'           => true,
                'force_activation'   => true,
                'force_deactivation' => true
            )
        );
        tgmpa( $plugins );
    });
    ```

Check ["Woodlets Seed"-Theme Tgm.php](https://github.com/Neochic/Woodlets-Seed/blob/master/src/services/Tgm.php) for a working example.

### Install manually
1. Go to [releases page](https://github.com/Neochic/Woodlets/releases) and download the latest bundled release.
2. Extract the zip archive to your WordPress plugins directory (usually "wp-content/plugins/").
3. Activate the plugin on the plugins page of your WordPress installation.

## Getting started
For the creation of new Woodlets themes we recommend to use ["Woodlets-CLI"](https://github.com/Neochic/Woodlets-CLI) instead of starting from scratch.
It might also be a good idea to take a look at the ["Woodlets Example"-Theme](https://github.com/Neochic/Woodlets-Example).

### Create a theme manually
1. Be sure the Woodlets plugin is [installed](#installation) and activated.
2. Create a new theme (as the [WordPress documentation says](https://codex.wordpress.org/Theme_Development#Basic_Templates) it should at least contain ```style.css``` and ```index.php```)
3. Put the following lines into your ```index.php```:

    ```php
    <?php
    do_action('neochic_woodlets_render_template');
    ```
    The action ```neochic_woodlets_render_template``` initializes the template rendering.
4. Activate your new theme.
5. Create your first page template with at least one column.
   Learn [how to create your own page templates](docs/page-templates.md).
6. Create your first Woodlets widget.
   Learn [how to create custom widgets](docs/widgets.md).
7. Add your new widget to the allowed widgets configuration of your page template column.
8. Create or edit a page and use your new page layout to test your widget in backend and frontend.

### Further Reading:
* [Widgets](docs/widgets.md)
* [Field types](docs/field-types.md)
* [Page templates](docs/page-templates.md)
* [Theme customization](docs/theme-customization.md)
* [Layouts](docs/layouts.md)
* [Post and other templates](docs/post-templates.md)
* [Multilanguage](docs/i18n.md)
* [Actions and filters](docs/actions-and-filters.md)
