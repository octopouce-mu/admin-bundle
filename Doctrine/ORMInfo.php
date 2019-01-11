<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */

namespace Octopouce\AdminBundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Octopouce\AdminBundle\Form\Type\AutoType;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ORMInfo
{
	/** @var EntityManagerInterface */
	private $entityManager;

	public function __construct( EntityManagerInterface $entityManager ) {
		$this->entityManager = $entityManager;
	}

	public function getFieldsConfig( string $class ): array {
		$fieldsConfig = [];
		$metadata     = $this->entityManager->getMetadataFactory()->getMetadataFor( $class );
		if ( ! empty( $fields = $metadata->getFieldNames() ) ) {
			$fieldsConfig = array_fill_keys( $fields, [] );
		}
		if ( ! empty( $assocNames = $metadata->getAssociationNames() ) ) {
			$fieldsConfig += $this->getAssocsConfig( $metadata, $assocNames );
		}

		return $fieldsConfig;
	}

	public function getAssociationTargetClass( string $class, string $fieldName ): string {
		$metadata = $this->entityManager->getMetadataFactory()->getMetadataFor( $class );
		if ( ! $metadata->hasAssociation( $fieldName ) ) {
			throw new \RuntimeException( sprintf( 'Unable to find the association target class of "%s" in %s.', $fieldName, $class ) );
		}

		return $metadata->getAssociationTargetClass( $fieldName );
	}

	private function getAssocsConfig( ClassMetadata $metadata, array $assocNames ): array {
		$assocsConfigs = [];
		foreach ( $assocNames as $assocName ) {
			if ( ! $metadata->isAssociationInverseSide( $assocName ) ) {
				continue;
			}
			$class = $metadata->getAssociationTargetClass( $assocName );
			if ( $metadata->isSingleValuedAssociation( $assocName ) ) {
				$nullable                    = ( $metadata instanceof ClassMetadataInfo ) && isset( $metadata->discriminatorColumn['nullable'] ) && $metadata->discriminatorColumn['nullable'];
				$assocsConfigs[ $assocName ] = [
					'field_type' => AutoType::class,
					'data_class' => $class,
					'required'   => ! $nullable,
				];
				continue;
			}
			$assocsConfigs[ $assocName ] = [
				'field_type'    => CollectionType::class,
				'entry_type'    => AutoType::class,
				'entry_options' => [
					'data_class' => $class,
				],
				'allow_add'     => true,
				'by_reference'  => false,
			];
		}

		return $assocsConfigs;
	}

}