<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 30/05/2018
 */

namespace Octopouce\AdminBundle\DependencyInjection;

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

		return $treeBuilder;
	}
}