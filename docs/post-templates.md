# Post and other templates
Since Woodlets replaces all WordPress Theme templates with Twig templates there are all the Twig templates for rendering posts, search results, 404 page, etc.

## Post details
Like page templates the templates for post details can also contain additional fields. And you can add multiple templates for different post types.
To create a post details template just add a template file to ```ẁoodlets/posts/```.  
They work exactly like the [page templates](page-templates.md), you can even create columns for advanced post layouts.

#### Example
```twig
{% extends '@woodlets/defaultTemplates/posts/default.twig' %}

{% block form %}
  {{
    woodlets.section("My additional configuration")
        .add('text', 'headline', {'label': 'Headline' })
  })
{% endblock %}

{% block view %}
  {{ woodlets.getPageConfig().headline }}
  {{ parent() }}
{% endblock %}

```

## Attachment
The attachment template is located at ```woodlets/attachment.twig```. It inherits by default from post details and displays the attached image instead of the post content.

## 404 - Not found page
The 404 template is located at ```ẁoodlets/404.twig```.

## List views
All the other templates like search, tag, category are different types of list templates and inherit by default from the list template.

They are located at: 
* ```woodlets/list.twig```
* ```woodlets/category.twig```
* ```woodlets/tag.twig```
* ```woodlets/archive.twig```
* ```woodlets/search.twig```
