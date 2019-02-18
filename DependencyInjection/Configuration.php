<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 30/05/2018
 */

namespace Octopouce\AdminBundle\DependencyInjection;

use Octopouce\AdminBundle\Form\UserType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode    = $treeBuilder->root( 'octopouce_admin' );

		$rootNode
			->children()
				->arrayNode('dashboard')
					->children()
						->booleanNode( 'enabled' )->defaultTrue()->end()
					->end()
				->end()
			->end();

		$this->addUserSection($rootNode);

		return $treeBuilder;
	}

	private function addUserSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('user')
					->addDefaultsIfNotSet()
					->canBeUnset()
					->children()
						->arrayNode('form')
							->addDefaultsIfNotSet()
							->children()
								->scalarNode('type')->defaultValue(UserType::class)->end()
								->scalarNode('name')->defaultValue('octopouce_admin_user_type')->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}
}
