# Layouts
Layouts should be used to display the basic HTML structure and all the stuff that is shared on multiple pages. Woodlets does provide a default layout that already renders a basic HTML page structure including ```wp_head()```, ```the_title()```, ```body_class()``` and ```wp_footer()```.

It's recommended to extend that default layout for the theme layouts and extend it to the specific needs. It contains a lot of blocks to extend its content.  
Just check the source of the [default layout ](https://github.com/Neochic/Woodlets/blob/master/views/defaultTemplates/layouts/default.twig) to learn more about the available blocks.

## Example
```twig
{# woodlets/layouts/default.twig #}
{% extends '@woodlets/defaultTemplates/layouts/default.twig' %}

{% block title %}{{ parent() }} - My special title{% endblock %}

{% block body %}
    <div class="head">
      <img class="logo" src="{{ woodlets.get_stylesheet_directory_uri() }}/img/logo.png" alt="My logo">
      {{ woodlets.wp_nav_menu({'theme_location': 'header-menu'}) }}
    </div>
    {{ parent() }}
    <div class="footer">
      {{ woodlets.wp_nav_menu({'theme_location': 'footer-menu'}) }}
    </div>
{% endblock %}
```
