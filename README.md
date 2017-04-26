{{ Twigony FrameworkBundle }}
=============================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a20684cb-83aa-486f-8bec-e0a4cd3ae307/mini.png)](https://insight.sensiolabs.com/projects/a20684cb-83aa-486f-8bec-e0a4cd3ae307)

Twigony is inspired by Symfony's [TemplateController](http://symfony.com/doc/3.3/templating/render_without_controller.html).
Twigony provides default controller actions for common use cases. You are able to configure Twigony through your
`routing.yml` file (like Symfony's TemplateController).

Goals of Twigony:

 * Easy to use (no own controllers are needed, an Entity and a template is all you need)
 * Usable for fast prototyping
 * Usable for simple pages
 * Much more frontend code (Twig) and less backend code (PHP/Symfony)
 * Covers common use cases (database listing, email…)

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

Documentation
-------------

 * [TemplateController](Resources/doc/TemplateController.md) for static pages (`_controller: twigony.template_controller:templateAction`)
 * [SecurityController](Resources/doc/SecurityController.md) for login page (`_controller: twigony.security_controller:loginAction`)
 * [SwiftMailerController](Resources/doc/SwiftMailerController.md) for email forms (`_controller: twigony.mailer_controller:emailAction`)
 * [DoctrineORMController](Resources/doc/DoctrineORMController.md) for database operations (`_controller: twigony.template_controller:*`)


Example usages
--------------

### Multiple static pages (TemplateController)

1.  Create two or more Twig templates (like `app/views/info/hello.html.twig` and `app/views/info/about.html.twig`)
2.  Extend your `routing.yml`:
        
        info_pages:
            path: '/info/{page}'
            defaults:
                _controller: 'twigony.template_controller:templateAction'
                template:    'info/{page}.html.twig'


### List entities (DoctrineORMController)

1.  [Create an entity](http://symfony.com/doc/3.3/doctrine.html#creating-an-entity-class)
    or use an existing one (e .g. `src/AppBundle/Entity/Post.php`)
2.  Create a template (like `app/views/posts/all.html.twig`)
3.  Extend your `routing.yml`:

        posts:
            path: '/posts'
            defaults:
                _controller: 'twigony.orm_controller:listAction'
                template: 'posts/all.html.twig'
                entity: 'AppBundle\Entity\Post'
                options:
                    as: 'posts' # Access variable for your Twig template. You can use it this way `{% for post in posts %}…`
                    perPage: 50

### Show single entity (DoctrineORMController)

1.  [Create an entity](http://symfony.com/doc/3.3/doctrine.html#creating-an-entity-class)
    or use an existing one (e .g. `src/AppBundle/Entity/Post.php`)
2.  Create a template (like `app/views/posts/show.html.twig`)
3.  Extend your `routing.yml`:

        show_post:
            path: '/posts/{id}' # Make sure, you are using "id" as id parameter!
            defaults:
                _controller: 'twigony.orm_controller:viewAction'
                template: 'posts/show.html.twig'
                entity:   'AppBundle\Entity\Post'
                options:
                    as: 'post' # Access variable for your Twig template. You can use it this way `{{ post.title }}…`
