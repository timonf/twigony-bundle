DoctrineORMController
=====================

Display a single entity (viewAction)
------------------------------------

Simplest way to display a single entity on a page.

You need:

 * Configured database in your Symfony Standard Edition
 * Configured Entity (something like `AppBundle\Entity\Post` located in `src/AppBundle/Entity/Post.php`)


### Complete configuration

```yaml
post: # Name of your route, should be unique
    path: '/blog/{id}'
    defaults:
        _controller: 'twigony.orm_controller:viewAction'
        template:    'blog/show.html.twig'
        entity:      'AppBundle\Entity\Blog'
        options:
            as: post
            roles: ['IS_AUTHENTICATED_ANONYMOUSLY', 'ROLE_USER']
            maxAge: 3600
            sharedAge: 3600
            private: false
```

### Explanation

| Parameter         | Required  | Purpose           | Description                                                      |
| ----------------- | --------- | ----------------- | ---------------------------------------------------------------- |
| `path`            | yes       | Routing           | Default Symfony Parameter                                        |
| `_controller`     | yes       | Routing           | Default Symfony Parameter                                        |
| `template`        | yes       | Twigony           | Path of the Twig template (should be located at `app/views`)     |
| `entity`          | yes       | Doctrine          | Full class name of entity to work with                           |
| `as`              | no        | Doctrine          | Access variable in Twig (default: `entity`)                      |
| `roles`           | no        | Security          | Array of roles allowed to access the given route                 |
| `maxAge`          | no        | Caching           | Max age for client caching                                       |
| `sharedAge`       | no        | Caching           | Max age for shared (proxy) caching                               |
| `private`         | no        | Caching           | Whether or not caching should apply for client caches only       |


Render a list of entities (listAction)
--------------------------------------

In many cases you need to list entries from the database (blog posts, news posts etc.). The "listAction" provides
an easy way to list entries from given entity.

You need:

 * Configured database in your Symfony Standard Edition
 * Configured Entity (something like `AppBundle\Entity\Post` located in `src/AppBundle/Entity/Post.php`)

### Complete configuration

```yaml
posts: # Name of your route, should be unique
    path: '/blog'
    defaults:
        _controller: 'twigony.orm_controller:listAction'
        template:    'blog/index.html.twig'
        entity:      'AppBundle\Entity\Blog'
        options:
            as: posts
            orderBy: ['publishedAt', 'DESC']
            perPage: 10
            roles: ['IS_AUTHENTICATED_ANONYMOUSLY']
            maxAge: 250
            sharedAge: 250
            private: false
```

### Explanation

| Parameter         | Required  | Purpose           | Description                                                      |
| ----------------- | --------- | ----------------- | ---------------------------------------------------------------- |
| `path`            | yes       | Routing           | Default Symfony Parameter                                        |
| `_controller`     | yes       | Routing           | Default Symfony Parameter                                        |
| `template`        | yes       | Twigony           | Path of the Twig template (should be located at `app/views`)     |
| `entity`          | yes       | Doctrine          | Full class name of entity to work with                           |
| `as`              | no        | Doctrine          | Access variable in Twig (default: `entities`)                    |
| `orderBy`         | no        | Doctrine          | Key to order the query. Example: `['publishedAt', 'DESC']`       |
| `perPage`         | no        | Pagination        | Entries per page. On `0` pagination is disabled. (default: `0`)  |
| `roles`           | no        | Security          | Array of roles allowed to access the given route                 |
| `maxAge`          | no        | Caching           | Max age for client caching                                       |
| `sharedAge`       | no        | Caching           | Max age for shared (proxy) caching                               |
| `private`         | no        | Caching           | Whether or not caching should apply for client caches only       |


Edit an entity (editAction)
---------------------------

Edit a given entity using a given form.

You need:

 * Configured database in your Symfony Standard Edition
 * Configured Entity (something like `AppBundle\Entity\Post` located in `src/AppBundle/Entity/Post.php`)
 * A Form class (something like `AppBundle\Form\PostType` located in `src/AppBundle/Form/PostType.php`)

### Complete configuration

```yaml
post_edit: # Name of your route, should be unique
    path: '/blog/{id}/edit'
    requirements:
        id: '/^[d]+$/'
    defaults:
        _controller: 'twigony.orm_controller:editAction'
        template:    'post/edit.html.twig'
        entity:      'AppBundle\Entity\Post'
        options:
            formClass: 'AppBundle\Form\PostType'
            roles: ['ROLE_ADMIN']
            flash: 'Post updated :)'
            redirect: 'posts'
            maxAge: ~
            sharedAge: ~
            private: true
```

### Explanation

| Parameter         | Required  | Purpose           | Description                                                      |
| ----------------- | --------- | ----------------- | ---------------------------------------------------------------- |
| `path`            | yes       | Routing           | Default Symfony Parameter, you can use {page} as parameter here  |
| `_controller`     | yes       | Routing           | Default Symfony Parameter                                        |
| `template`        | yes       | Twigony           | Path of the Twig template (should be located at `app/views`)     |
| `entity`          | yes       | Twigony           | Full class name of entity/data model to work with                |
| `formClass`       | should    | Twigony           | Form class to render. Will be available as `form`                | 
| `redirect`        | no        | RedirectResponse  | Path to redirect to after submitting data (on success)           |
| `flash`           | no        | FlashMessenger    | Message to add as 'notice' to session's FlashBag (on success)    |
| `roles`           | no        | Security          | Array of roles allowed to access the given route                 |
| `maxAge`          | no        | Caching           | Max age for client caching                                       |
| `sharedAge`       | no        | Caching           | Max age for shared (proxy) caching                               |
| `private`         | no        | Caching           | Whether or not caching should apply for client caches only       |


Create an entity (editAction)
-----------------------------

Creates a new entity. Useful for feedback, comment or participant forms.

You need:

 * Configured database in your Symfony Standard Edition
 * Configured Entity (something like `AppBundle\Entity\Post` located in `src/AppBundle/Entity/Post.php`)
 * A Form class (something like `AppBundle\Form\PostType` located in `src/AppBundle/Form/PostType.php`)

### Complete configuration

```yaml
post_create: # Name of your route, should be unique
    path: '/blog/create-post'
    defaults:
        _controller: 'twigony.orm_controller:editAction'
        template:    'post/edit.html.twig'
        entity:      'AppBundle\Entity\Post'
        options:
            formClass: 'AppBundle\Form\PostType'
            roles: ['ROLE_ADMIN', 'ROLE_MODERATOR']
            flash: 'Post created :)'
            redirect: 'posts'
            maxAge: ~
            sharedAge: ~
            private: true
```

### Explanation

| Parameter         | Required  | Purpose           | Description                                                      |
| ----------------- | --------- | ----------------- | ---------------------------------------------------------------- |
| `path`            | yes       | Routing           | Default Symfony Parameter, you can use {page} as parameter here  |
| `_controller`     | yes       | Routing           | Default Symfony Parameter                                        |
| `template`        | yes       | Twigony           | Path of the Twig template (should be located at `app/views`)     |
| `entity`          | yes       | Twigony           | Full class name of entity/data model to work with                |
| `formClass`       | should    | Twigony           | Form class to render. Will be available as `form`                | 
| `redirect`        | no        | RedirectResponse  | Path to redirect to after submitting data (on success)           |
| `flash`           | no        | FlashMessenger    | Message to add as 'notice' to session's FlashBag (on success)    |
| `roles`           | no        | Security          | Array of roles allowed to access the given route                 |
| `maxAge`          | no        | Caching           | Max age for client caching                                       |
| `sharedAge`       | no        | Caching           | Max age for shared (proxy) caching                               |
| `private`         | no        | Caching           | Whether or not caching should apply for client caches only       |


Deleting an entity
------------------

Not implemented yet. If you want to build a backend interface, you should consider using EasyAdminBundle.
Twigony is supposed for simple frontend pages. 


See also
--------

 * [Configure an entity](http://symfony.com/doc/current/doctrine.html#add-mapping-information) (Symfony Docs)
 * [EasyAdminBundle](https://github.com/javiereguiluz/EasyAdminBundle): If you want a simple backend. With this bundle
   you don't even need to create Twig templates.
