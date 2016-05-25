# Actions and filters

## Actions
#### Theme customizer
The ```neochic_woodlets_theme``` action is called to configure the Theme Customizer. It gets a theme configuration object as parameter. Check [theme customization](theme-customization.md) for details.

## Filters
#### Field types
The ```neochic_woodlets_field_types``` filter can be used to extend Woodlets with own field types.

```php
add_filter('neochic_woodlets_field_types', function ($fieldTypes) {
  $fieldTypes['customFieldType'] = new \Neochic\Woodlets\FieldTypes\FieldType('custom', 'my-plugin');
  return $fieldTypes;
});
```

#### Twig
The ```neochic_woodlets_twig``` filter is for [extending Twig with functions and filters](http://twig.sensiolabs.org/doc/advanced.html).
This is the main way to create dynamic widgets. You may for example provide a function to retrieve a Facebook stream and use it in your widget.

```php
add_filter('neochic_woodlets_twig_p', function ($twig) {
  $twig->addFunction(new \Twig_SimpleFunction('hello', function ($val) {
        return $val . " world!";
  }));

  return $twig;
});
```

#### Twig paths
With the ```neochic_woodlets_twig_paths``` filter additional template paths can be added to the twig loader. This is useful if you write a plugin
that provides generic templates, widgets or field types.

```php
add_filter('neochic_woodlets_twig_paths', function ($paths) {
  array_push($paths, array(
    "path" => plugin_dir_path( __FILE__ ) . "woodlets/views/",
    "namespace" => "my-plugin"
  ));
  return $paths;
});
```

#### Page templates
The ```neochic_woodlets_page_templates``` filter can be used to add templates as page templates that are not in the ```woodlets/pages``` directory.

```php
add_filter('neochic_woodlets_page_templates', function ($templates) {
  array_push($templates, "@__main__/some-special-location/special-page.twig");
  return $templates;
});
```

#### Post templates
The ```neochic_woodlets_post_templates``` filter can be used to add templates as post templates that are not in the ```woodlets/posts``` directory.

```php
add_filter('neochic_woodlets_post_templates', function ($templates) {
  array_push($templates, "@__main__/some-special-location/special-post.twig");
  return $templates;
});
```

#### Default template
The ```neochic_woodlets_default_template_${type}``` is a dynamic filter that can be used to overwrite the default templates for different page types. Available types are ```page```, ```post```, ```attachment```, ```404```, ```category```, ```tag```, ```archive```, ```search``` and ```list```.

```php
add_filter('neochic_woodlets_default_template_page', function ($template) {
  return 'pages/not-the-default.twig';
});
```

#### Template
The ```neochic_woodlets_template``` filter is called after the template for the render template action is determined. You can use this filter to overwrite the selected template in special situations.

```php
add_filter('neochic_woodlets_template', function ($template) {
    if (is_tag()) {
      return 'some-other-tag-template.twig';
    }
    return $template;
});
```

####  Rich Text Editor type default settings
The ```neochic_woodlets_rte_settings``` filter can be used to set the default settings for the [Rich Text Editor type](fielt-types.md#rich-text-editor).

```php
add_filter('neochic_woodlets_rte_settings', function ($settings) {
    $settings['toolbar1'] = 'bold, italic';
    $settings['autoresize_min_height'] = 200;
    return $settings;
});
```

#### WordPress Media Selector type default settings
The ```neochic_woodlets_media_settings``` filter can be used to set the default settings for the [WordPress Media Selector type](fielt-types.md#wordpress-media-selector).

```php
add_filter('neochic_woodlets_media_settings', function ($settings) {
    $settings['library']['type'] = 'image';
    return $settings;
});
```

#### Debug
The ```neochic_woodlets_debug``` filter can be used to enable debug mode for Woodlets. It's only needed for Woodlets development. You shouldn't use this filter unless you know what you're doing.  

```php
add_filter('neochic_woodlets_debug', function () {
    return true;
});
```

## Render template actions
The ```neochic_woodlets_render_template```-action is special, since it's getting called by the theme instead of Woodlets. So you need to do a ```do_action()``` where the template should be rendered. This is usually in the ```index.php``` of the theme to init rendering of the content.

Read the [getting started](../README.md#getting-started) guide for an example on how to use this action.
