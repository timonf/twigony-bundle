SecurityController
==================

Render a custom login template (loginAction)
--------------------------------------------

This controller are using the same snippet as it is in the [Symfony Doc](http://symfony.com/doc/current/security/form_login_setup.html).
With Twigony you don't need to copy-paste the entire controller.

### Minimal configuration

```yaml
login: # Name of your route, should be unique
    path: '/login'
    defaults:
        _controller: 'twigony.security_controller:loginAction'
        template:    'security/login.html.twig'
```

### Explanation

| Parameter         | Required  | Purpose           | Description                                                      |
| ----------------- | --------- | ----------------- | ---------------------------------------------------------------- |
| `path`            | yes       | Routing           | Default Symfony Parameter                                        |
| `_controller`     | yes       | Routing           | Default Symfony Parameter                                        |
| `template`        | yes       | Twigony           | Path of the Twig template (should be located at `app/views`)     |


See also
--------

 * See how to create the template: [Symfony Doc](http://symfony.com/doc/current/security/form_login_setup.html)
