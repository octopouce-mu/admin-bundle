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

		$this->setDashboard($container, $config);
	}

	private function setDashboard( $container, $config ) {
		$dashboardService = $container->getDefinition('Octopouce\AdminBundle\Service\DashboardService');
		$dashboardService->addMethodCall('setEnabled', [$config['dashboard']['enabled']]);

		$adminExtension = $container->getDefinition('Octopouce\AdminBundle\Twig\AdminExtension');
		$adminExtension->addMethodCall('setDashboardEnabled', [$config['dashboard']['enabled']]);
	}
}