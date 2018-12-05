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
				->scalarNode( 'project_name' )->isRequired()->end()
				->scalarNode( 'project_domain' )->isRequired()->end()
				->scalarNode( 'google_ga_json' )->end()
				->scalarNode( 'google_ga_view' )->end()
				->scalarNode( 'facebook_id' )->end()
				->scalarNode( 'facebook_secret' )->end()
				->scalarNode( 'twitter_oauth_token' )->end()
				->scalarNode( 'twitter_oauth_secret' )->end()
				->scalarNode( 'twitter_consumer_key' )->end()
				->scalarNode( 'twitter_consumer_secret' )->end()
			->end();

		return $treeBuilder;
	}
}