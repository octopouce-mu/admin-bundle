<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octopouce\AdminBundle\Repository\Account;

use App\Entity\Account\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

	public function countByCreatedAtTo($firstDay, $lastDay){

		$qb = $this->createQueryBuilder('u');
		$qb->select('count(u.id)')
		   ->where('u.createdAt >= :firstDay')
		   ->andWhere('u.createdAt < :lastDay')
		   ->setParameter('firstDay', $firstDay)
		   ->setParameter('lastDay', $lastDay);

		return $qb->getQuery()->getSingleScalarResult();
	}

	public function countAll(){

		$qb = $this->createQueryBuilder('u');
		$qb->select('count(u.id)');

		return $qb->getQuery()->getSingleScalarResult();
	}

}
