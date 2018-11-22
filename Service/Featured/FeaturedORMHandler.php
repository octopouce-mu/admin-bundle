<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/11/2018
 */

namespace Octopouce\AdminBundle\Service\Featured;

use Doctrine\ORM\EntityManagerInterface;

class FeaturedORMHandler
{
	/**
	 * @var EntityManagerInterface
	 */
	protected $em;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->em = $entityManager;
	}

	public function setFeatured($entity, $setter, $id, $featured)
	{
		$qb = $this->em->createQueryBuilder();

		$q = $qb->update($entity, 'u')
		        ->set('u.'.$setter, $qb->expr()->literal($featured))
		        ->where('u.id = ?1')
		        ->setParameter(1, $id)
		        ->getQuery();

		try {
			$p = $q->execute();
		}
		catch(\Doctrine\ORM\NoResultException $e) {
			return false;
		}

		return true;
	}
}