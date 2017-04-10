WIP {{ Twigony FrameworkBundle }}
=================================

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

TODO: Add example routing configurations here
