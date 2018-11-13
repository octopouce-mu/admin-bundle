AdminBundle
===============

Introduction
------------
This Symfony bundle offers a manage website admin.

## Prerequisites

This version of the bundle requires Symfony Flex. 

##Installation

### Step 1: Download AdminBundle using composer
This library is available on [Packagist](http://packagist.org/packages/octopouce-mu/admin-bundle).

```bash
composer require octopouce-mu/admin-bundle
```

Composer will install the bundle to your project's `vendor/` directory.

### Step 2: Create your User and Invitation class
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

### Step 3: Update database
```
php bin/console doctrine:schema:update --force
```
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

### Step 4: Import Octopouce Admin routing file
Now that you have activated and configured the bundle, all that is left to do is import the routing files.
```yaml
# config/routes/octopouce.yaml

_octopouce_admin:
    resource: "@OctopouceAdminBundle/Resources/config/routing.yaml"
    prefix: /admin
```

### Step 5: Publish the Assets
Now that you have activated and configured the bundle, all that is left to do is import the routing files.
```
php bin/console assets:install --symlink
```

### Step 6: Config the file security
You does to config the provider and encoder in security and the access
```yaml
# config/packages/security.yaml

security:
    encoders:
        # Our user class and the algorithm we'll use to encode passwords
        # https://symfony.com/doc/current/security.html#c-encoding-the-user-s-password
        App\Entity\Account\User: bcrypt

    providers:
        # https://symfony.com/doc/current/security.html#b-configuring-how-users-are-loaded
        # In this example, users are stored via Doctrine in the database
        # To see the users at src/App/DataFixtures/ORM/LoadFixtures.php
        # To load users from somewhere else: https://symfony.com/doc/current/security/custom_provider.html
        database_users:
            entity: { class: App\Entity\Account\User, property: username }

    role_hierarchy:
        ROLE_CMS: ROLE_USER
        ROLE_BLOG: ROLE_USER
        ROLE_ADVERT: ROLE_USER
        ROLE_ADMIN: [ROLE_CMS, ROLE_BLOG, ROLE_ADVERT]
        ROLE_SUPER_ADMIN: ROLE_ADMIN

    # https://symfony.com/doc/current/security.html#initial-security-yml-setup-authentication
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            # this firewall applies to all URLs
            pattern: ^/

            # but the firewall does not require login on every page
            # denying access is done in access_control or in your controllers
            anonymous: true

            # This allows the user to login by submitting a username and password
            # Reference: https://symfony.com/doc/current/security/form_login_setup.html
            form_login:
                # The route name that the login form submits to
                check_path: octopouce_admin_login_admin
                # The name of the route where the login form lives
                # When the user tries to access a protected page, they are redirected here
                login_path: octopouce_admin_login_admin
                # Secure the login form against CSRF
                # Reference: https://symfony.com/doc/current/security/csrf_in_login_form.html
                csrf_token_generator: security.csrf.token_manager
                # The page users are redirect to when there is no previous page stored in the
                # session (for example when the users access directly to the login page).
                failure_path: octopouce_admin_login_admin
                use_referer: true

            logout:
                # The route name the user can go to in order to logout
                path: octopouce_admin_logout
                # The name of the route to redirect to after logging out
                target: octopouce_admin_login_admin

    access_control:
        # this is a catch-all for the admin area
        # additional security lives in the controllers
        - { path: '^/admin/login', roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: '^/admin', roles: ROLE_ADMIN }
```
### Step 6: Load fixtures
Add a translator in framework.yaml
```yaml
framework:
    default_locale: en
    translator:
        fallbacks: ['en']
```

### Step 7: Load fixtures
You can to generate fixtures data in database
```
php bin/console doctrine:fixtures:load
```