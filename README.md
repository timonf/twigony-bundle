WIP {{ Twigony FrameworkBundle }}
=================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a20684cb-83aa-486f-8bec-e0a4cd3ae307/mini.png)](https://insight.sensiolabs.com/projects/a20684cb-83aa-486f-8bec-e0a4cd3ae307)

Twigony is inspired by Symfony's [TemplateController](http://symfony.com/doc/3.3/templating/render_without_controller.html).
Twigony provides default Controller actions for common use cases. You are able to configure Twigony through your
`routing.yml` file (like Symfony's TemplateController).

Goals of Twigony:

 * Easy to use (no own controllers are needed, an Entity and a template is all you need)
 * Usable for fast prototyping
 * Usable for simple pages
 * Much more frontend code (Twig) and less backend code (PHP/Symfony)
 * Covers common use cases (database listing, email –todo–…)

---------------------------------------

**Information:** This project is in development. If you want to support me
or this project contact me on [Slack](https://symfony-devs.slack.com) (My name: @timon).
If you don't have access to Slack, have a look [here](http://symfony.com/support).

---------------------------------------

Installation
------------

Create an empty project and use composer to install this bundle:

```console
$ symfony new your-new-project
$ composer require timonf/twigony-framework-bundle
```

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new \Twigony\Bundle\FrameworkBundle\TwigonyFrameworkBundle(),
        );
    }
}
```

Usage
-----

### Multiple static pages (TemplateController)

1.  Create two or more Twig templates (like `app/views/info/hello.html.twig` and `app/views/info/about.html.twig`)
2.  Extend your `routing.yml`:
        
        info_hello:
          page:
            path: '/info/{page}'
            defaults:
              _controller: 'TwigonyFrameworkBundle:Default:page'
              template:    'info/{page}.html.twig'


### List entities (DoctrineORMController)

1.  [Create an entity](http://symfony.com/doc/3.3/doctrine.html#creating-an-entity-class)
    or use an existing one (e .g. `src/AppBundle/Entity/Post.php`)
2.  Create a template (like `app/views/posts/all.html.twig`)
3.  Extend your `routing.yml`:

        posts:
          path: '/posts'
          defaults:
            _controller: 'TwigonyFrameworkBundle:Default:list'
            template: 'posts/all.html.twig'
            entity:   'AppBundle\Entity\Post'
            options:
              as: 'posts' # Access variable for your Twig template. You can use it this way `{% for post in posts %}…`
              form_class: 'AppBundle/Form/YourPostType' # If you want a custom form

### Show single entity (DoctrineORMController)

1.  [Create an entity](http://symfony.com/doc/3.3/doctrine.html#creating-an-entity-class)
    or use an existing one (e .g. `src/AppBundle/Entity/Post.php`)
2.  Create a template (like `app/views/posts/show.html.twig`)
3.  Extend your `routing.yml`:

        show_post:
          path: '/posts/{id}' # Make sure, you are using "id" as id parameter!
          defaults:
            _controller: 'TwigonyFrameworkBundle:Default:view'
            template: 'posts/show.html.twig'
            entity:   'AppBundle\Entity\Post'
            options:
              as: 'post' # Access variable for your Twig template. You can use it this way `{{ post.title }}…`
