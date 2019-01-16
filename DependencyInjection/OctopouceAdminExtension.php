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
		$this->setLocale($container);
	}

	private function setDashboard( $container, $config ) {
		$dashboardService = $container->getDefinition('Octopouce\AdminBundle\Service\DashboardService');
		$dashboardService->addMethodCall('setEnabled', [$config['dashboard']['enabled']]);

		$adminExtension = $container->getDefinition('Octopouce\AdminBundle\Twig\AdminExtension');
		$adminExtension->addMethodCall('setDashboardEnabled', [$config['dashboard']['enabled']]);
	}

	private function setLocale( ContainerBuilder $container ) {
		$localeProvider = $container->getDefinition('Octopouce\AdminBundle\Provider\Locale\LocaleProvider');

		if($container->hasParameter('app_locales')) {
			$localeProvider->addMethodCall('setLocales', [$container->getParameter('app_locales')]);
		} else {
			$container->setParameter('app_locales', [$container->getParameter('locale')]);
			$localeProvider->addMethodCall('setLocales', [$container->getParameter('locale')]);
		}

		$container->setParameter('knp.doctrine_behaviors.translatable_subscriber.translatable_trait', 'Octopouce\AdminBundle\Translatable\Translatable');
	}
}