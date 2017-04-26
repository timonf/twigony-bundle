SwiftMailerController
=====================

Render form and send email on success (emailAction)
---------------------------------------------------

Using the SwiftMailerController you are able to send submitted form data as an email to a configured/static address.
This controller is very useful for contact and feedback pages.

### Minimal configuration

```yaml
example_email: # Name of your route, should be unique
    path: '/contact'
    defaults:
        _controller: 'twigony.mailer_controller:emailAction'
        template:    'page/contact.html.twig'
        options:
            formClass: 'AppBundle/Form/ContactType'
            entity: 'AppBundle/Entity/Contact'
            subject: 'New message from Webpage!'
            emailTemplate: 'mail/contact.html.twig'
            to: 'sales@example.com'
```

### Complete configuration

```yaml
example_email_max: # Name of your route, should be unique
    path: '/contact2'
    defaults:
        _controller: 'twigony.mailer_controller:emailAction'
        template:    'page/contact.html.twig'
        entity: 'AppBundle/Entity/Contact'
        options:
            as: 'contact_data'
            formClass: 'AppBundle/Form/ContactType'
            emailTemplate: 'mail/contact.html.twig'
            from: 'noreply@example.com'
            to: 'sales@example.com'
            subject: 'New message from Webpage!'
            message: 'Thank you for your request!'
            redirect: 'index'
            persist: true
            roles: ['IS_AUTHENTICATED_ANONYMOUSLY']
            maxAge: 0
            sharedAge: 0
            private: true
```

### Explanation

| Parameter         | Required  | Purpose           | Description                                                      |
| ----------------- | --------- | ----------------- | ---------------------------------------------------------------- |
| `path`            | yes       | Routing           | Default Symfony Parameter                                        |
| `_controller`     | yes       | Routing           | Default Symfony Parameter                                        |
| `template`        | yes       | Twigony           | Path of the Twig template (should be located at `app/views`)     |
| `entity`          | yes       | Twigony           | Full class name of entity/data model to work with                |
| `formClass`       | should    | Twigony           | Form class to render. Will be available as `form`                | 
| `persist`         | no        | Doctrine          | Submitted data can also persisted to Database (default: `false`) |
| `redirect`        | no        | RedirectResponse  | Path to redirect to after submitting data (on success)           |
| `flash`           | no        | FlashMessenger    | Message to add as 'notice' to session's FlashBag (on success)    |
| `emailTemplate`   | yes       | SwiftMailer       | Email content (including submitted data)                         |
| `as`              | no        | SwiftMailer       | Data accessor variable for email template (default `entity`)     |
| `to`              | yes       | SwiftMailer       | Recipient of the email                                           |
| `subject`         | yes       | SwiftMailer       | Subject of the email                                             |
| `from`            | no        | SwiftMailer       | Sender of the email (default: same as `to`)                      |
| `roles`           | no        | Security          | Array of roles allowed to access the given route                 |
| `maxAge`          | no        | Caching           | Max age for client caching                                       |
| `sharedAge`       | no        | Caching           | Max age for shared (proxy) caching                               |
| `private`         | no        | Caching           | Whether or not caching should apply for client caches only       |


See also
--------

 * Use [Symfony Constraints](http://symfony.com/doc/current/reference/constraints.html) to validate your form data.
 * [GregwarCaptchaBundle](https://github.com/Gregwar/CaptchaBundle): Simple Captcha extension and easy to add.
