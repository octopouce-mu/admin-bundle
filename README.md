OctopouceAdminBundle
====================

Prerequisites
-------------

This version of the bundle requires Symfony Flex (>= 4.0) and PHP 7.
You want to use Doctrine ORM and MySQL.

## Translations


If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

```yaml
# app/config/config.yml

framework:
    default_locale: en
    translator:
        fallbacks: ['en']
```

```yaml
# app/config/services.yml

parameters:
    app_locales: en|fr
    locale: 'fr'
```

For more information about translations, check [Symfony documentation](https://symfony.com/doc/current/book/translation.html).

Installation
------------

1. Download OctopouceAdminBundle using composer
2. Create your User and Invitation class
3. Update your database schema
4. Import OctopouceAdminBundle routing
5. Publish the Assets
6. Configure your file security
7. Generate Data Fixtures
8. Configure your .env

## Step 1: Download OctopouceAdminBundle using composer

Require the bundle with composer:

```bash
$ composer require octopouce-mu/admin-bundle
```

## Step 2: Create your User and Invitation class

The goal of this bundle is to persist some ``User`` class to a database.
Your first job, then, is to create the ``User`` and ``Invitation`` class
for your application. This class can look and act however you want: add any
properties or methods you find useful. This is *your* ``User`` & ``Invitation`` class.

The bundle provides base classes which are already mapped for most fields
to make it easier to create your entity. Here is how you use it:

1. Extend the base class (from the ``Model`` folder if you are using
   any of the doctrine variants)
2. Map the ``id`` field. It must be protected as it is inherited from the parent class.

**Note**

The doc uses a bundle named ``AppBundle`` according to the Symfony best
practices. However, you can of course place your user class in the bundle
you want.

**Caution:**

If you override the __construct() method in your User class, be sure
to call parent::__construct(), as the base User class depends on
this to initialize some fields.

### a) Doctrine ORM User class

If you're persisting your users via the Doctrine ORM, then your ``User`` class
should live in the ``Entity`` namespace of your bundle and look like this to
start:

```php
<?php
// src/Entity/Account/User

namespace App\Entity\Account;

use Octopouce\AdminBundle\Entity\Account\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="account_user")
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
    }
}
```


### b) Doctrine ORM Invitation class

If you're persisting your users via the Doctrine ORM, then your ``Invitation`` class
should live in the ``Entity`` namespace of your bundle and look like this to
start:

```php
<?php
// src/Entity/Account/Invitation

namespace App\Entity\Account;

use Octopouce\AdminBundle\Entity\Account\Invitation as BaseInvitation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="account_invitation")
 */
class Invitation extends BaseInvitation
{
    public function __construct()
    {
        parent::__construct();
    }
}
```

## Step 3: Update your database schema

For ORM run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

**Caution**

If error "1071 Specified key was too long; max key length is 767 bytes", you change configs doctrine :


```yaml
# config/packages/doctrine.yaml

doctrine:
    dbal:
        charset: utf8
        default_table_options:
            charset: utf8
            collate: utf8_unicode_ci
```

## Step 4: Import OctopouceAdminBundle routing files

Now that you have activated and configured the bundle, all that is left to do is
import the OctopouceAdminBundle routing files if Symfony Flex hasn't already imported the file.

```yaml
# config/routes/octopouce.yaml

_octopouce_admin:
    resource: "@OctopouceAdminBundle/Resources/config/routing/routing.yaml"
```

## Step 5: Publish the Assets

```bash
$ php bin/console assets:install --symlink
```

## Step 6: Configure your file security

### a. Configure default for admin and user

```yaml
# config/packages/security.yaml

security:
    encoders:
        App\Entity\Account\User: bcrypt

    providers:
        database_users:
            entity: { class: App\Entity\Account\User, property: email }

    role_hierarchy:
        ROLE_ADMIN: ROLE_USER
        ROLE_SUPER_ADMIN: ROLE_ADMIN

    # https://symfony.com/doc/current/security.html#initial-security-yml-setup-authentication
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        admin:
            pattern: ^/admin/
            
            # you can change your user checker here
            user_checker: Octopouce\AdminBundle\Security\UserChecker
            
            anonymous: true
            form_login:
                check_path: octopouce_admin_login_admin
                login_path: octopouce_admin_login_admin
                csrf_token_generator: security.csrf.token_manager
                failure_path: octopouce_admin_login_admin
                use_referer: true
                default_target_path: octopouce_admin_dashboard_index
            logout:
                path: octopouce_admin_logout
                target: octopouce_admin_login_admin
                
        # you can config user control in frontend here, exemple :
        #main:
        #    pattern: ^/
        #    user_checker: App\Security\UserChecker
        #    anonymous: true
        #    form_login:
        #        check_path: security_login
        #        login_path: security_login
        #        csrf_token_generator: security.csrf.token_manager
        #        failure_path: security_login
        #        use_referer: true
        #        default_target_path: account_index
        #    logout:
        #        path: security_logout
        #        target: homepage

    access_control:
        # this is a catch-all for the admin area
        # additional security lives in the controllers
        - { path: '^/admin/login', roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: '^/admin', roles: ROLE_ADMIN }
```

## Step 7: Generate Data Fixtures

The bundle need data default for working so uou can to generate fixtures data in database :

```bash
$ php bin/console doctrine:fixtures:load
```

##Step 8: Configure your .env

For finish the configuration of OctopouceAdminBundle, you can to configure package dependencies in .env.


Others bundles
--------------

You can to add bundles with OctopouceAdminBundle :

- [OctopouceCmsBundle](https://github.com/octopouce-mu/cms-bundle)
- [OctopouceBlogBundle](https://github.com/octopouce-mu/blog-bundle)
- [OctopouceAdvertisingBundle](https://github.com/octopouce-mu/advertising-bundle)
