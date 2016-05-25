# Theme customization
You may use the simplified API of Woodlets to add controls to the WordPress Theme Customizer. The main benefit is, that you can use the same [field types](field-types.md) as for the widgets and page fields.

*Note: There is no support for panels in Woodlets. If you need panels it's recommended to use the native WordPress API to add the form controls.*

The configuration of the fields for the Customizer is nearly the same as for the [additional page fields](page-templates.md). But since there is no main template for the whole theme it's configured via PHP instead of a Twig template. Therefore the syntax is slightly different.

To add controls to your theme hook into the ```neochic_woodlets_theme``` action. You'll get a theme configuration object as parameter.

#### Methods of the theme configuration object
* ```section(string $title, string $id, int $priority)``` - Add a section to the customizer. This method returns a form configurator object.

#### Methods of the form configuration object
* ```add(string $type, string $name, array $config)``` - Adds a form field to the section. (Check [field types documentation](field-types.md) for available types and configurations.)  

## Example
```php
add_action('neochic_woodlets_theme', function ($themeConfig) {
    $contactSection = $themeConfig->section('Contact', 'contact');
    $contactSection->add('text', 'contactEmail', array('label' => 'E-Mail'));
    $contactSection->add('text', 'contactPhone', array('label' => 'Phone'));

    $footerSection = $themeConfig->section('Footer', 'footer');
    $footerSection->add('text', 'footerText', array('label' => 'Footer text'));
});
```
