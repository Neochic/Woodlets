=== Woodlets ===
Contributors: christophstickel
Tags: theme development, content, layout, widget, widgets
Requires at least: 4.6.1
Tested up to: 4.6.1
License: MIT License
License URI: https://github.com/Neochic/Woodlets/blob/master/LICENSE

Create WordPress Page Layouts using Twig Templates

== Description ==

Woodlets is a WordPress plugin that makes theme development more productive and fun.
The main features are:
* Heavily Twig based theme development
* Create widgets with a single Twig-Template file
* Multi column page layouts
* Custom page template fields with data inheritance
* Simple way to add controls to the theme customizer
* Compatible with native WordPress widgets

== Installation ==
1. Be sure the Woodlets plugin is activated.
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

For the creation of new Woodlets themes you may download the ["Woodlets Seed"-Theme](https://github.com/Neochic/Woodlets-Seed) instead of starting from scratch.

== Changelog ==

= 0.2.4 =
* made plugin ready to be released on WordPress plugin repository
