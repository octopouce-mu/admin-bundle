<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/11/2018
 */

namespace Octopouce\AdminBundle\Service\Sortable;

use Doctrine\ORM\EntityManagerInterface;

class PositionORMHandler extends PositionHandler
{
	/**
	 * @var EntityManagerInterface
	 */
	protected $em;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->em = $entityManager;
	}

	public function setPosition($entity, $setter, $dataPositionList)
	{

		foreach ($dataPositionList as $position){

			$editId = (int) $position['id'];
			$position = (int) $position['position'];

			$qb = $this->em->createQueryBuilder();
			$q = $qb->update($entity, 'u')
			        ->set('u.'.$setter, $qb->expr()->literal($position))
			        ->where('u.id = ?1')
			        ->setParameter(1, $editId)
			        ->getQuery();


			try {
				$p = $q->execute();
			}
			catch(\Doctrine\ORM\NoResultException $e) {
				return false;
			}

		}

		return true;
	}
}