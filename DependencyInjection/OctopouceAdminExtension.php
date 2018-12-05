<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 30/05/2018
 */

namespace Octopouce\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class OctopouceAdminExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('services.yaml');

		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);

		$this->setParamater($container, $config);

//		$this->addAnnotatedClassesToCompile(array(
//			'**AdminBundle\\Controller\\'
//		));

	}

	private function setParamater($container, $config) {
		$container->setParameter('project.name', $config['project_name']);
		$container->setParameter('project.domain', $config['project_domain']);

		$container->setParameter('google.ga.json', $config['google_ga_json']);
		$container->setParameter('google.ga.view', $config['google_ga_view']);

		$container->setParameter('facebook.id', $config['facebook_id']);
		$container->setParameter('facebook.secret', $config['facebook_secret']);

		$container->setParameter('twitter.oauth.token', $config['twitter_oauth_token']);
		$container->setParameter('twitter.oauth.secret', $config['twitter_oauth_secret']);
		$container->setParameter('twitter.consumer.key', $config['twitter_consumer_key']);
		$container->setParameter('twitter.consumer.secret', $config['twitter_consumer_secret']);
	}
}