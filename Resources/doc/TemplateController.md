TemplateController
==================

Twigony's TemplateController offers two ways of embedding templates:

 - A single, static page (`path: /my-page`, `template: my-page.html.twig`)
 - Multiple pages at once (`path: /some-place/{page}`, `template: pages/{page}.html.twig`).
   You can put multiple templates in `app/views/pages` (for this example) and Twigony will render a template
   from the requested `{page}` parameter. If the template does not exist, Symfony's 404 page will be returned.

Complete usage
--------------

```yaml
foo_bar: # Name of your route, should be unique
    path: '/info/{page}'
    defaults:
        _controller: 'twigony.template_controller:templateAction'
        template:    'info/{page}.html.twig'
        options:
            maxAge: 250
            sharedAge: 250
            private: false
```

Explanation
-----------

| Parameter         | Required  | Purpose           | Description                                                      |
| ----------------- | --------- | ----------------- | ---------------------------------------------------------------- |
| `path`            | yes       | Routing           | Default Symfony Parameter, you can use {page} as parameter here  |
| `_controller`     | yes       | Routing           | Default Symfony Parameter                                        |
| `template`        | yes       | Twigony Template  | Path of the Twig template (should be located at `app/views`)     |
| `maxAge`          | no        | Caching           | Max age for client caching                                       |
| `sharedAge`       | no        | Caching           | Max age for shared (proxy) caching                               |
| `private`         | no        | Caching           | Whether or not caching should apply for client caches only       |
