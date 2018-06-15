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
```bash
php bin/console doctrine:schema:update --force
```

### Step 4: Import Octopouce Admin routing file
Now that you have activated and configured the bundle, all that is left to do is import the routing files.
```yml
# config/routes/octopouce.yaml

_octopouce_admin:
    resource: "@OctopouceAdminBundle/Resources/config/routing.yaml"
```

### Step 5: Publish the Assets
Now that you have activated and configured the bundle, all that is left to do is import the routing files.
```bash
php bin/console assets:install --symlink
```