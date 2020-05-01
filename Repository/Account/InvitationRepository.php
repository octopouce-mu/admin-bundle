<?php

namespace Octopouce\AdminBundle\Repository\Account;

use App\Entity\Account\Invitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Invitation|null find( $id, $lockMode = null, $lockVersion = null )
 * @method Invitation|null findOneBy( array $criteria, array $orderBy = null )
 * @method Invitation[]    findAll()
 * @method Invitation[]    findBy( array $criteria, array $orderBy = null, $limit = null, $offset = null )
 */
class InvitationRepository extends ServiceEntityRepository {
	public function __construct( ManagerRegistry $registry ) {
		parent::__construct( $registry, Invitation::class );
	}


	public function findOneIfExistAndActive( string $email ): ?Invitation {
		$qb = $this->createQueryBuilder( 'i' );

		$now = new \DateTime();
		$now->modify( '-1 month' );

		$qb->andWhere( 'i.email LIKE :email' )
		   ->setParameter( 'email', $email )
		   ->andWhere( 'i.sent = 1' )
		   ->andWhere( 'i.confirm = 0' )
		   ->andWhere( 'i.createdAt > :now' )
		   ->setParameter( 'now', $now );

		return $qb->getQuery()->getOneOrNullResult();
	}
}
