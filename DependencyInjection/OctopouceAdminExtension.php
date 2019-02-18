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

		if (!empty($config['user'])) {
			$this->loadUser($config['user'], $container, $loader);
		}
	}

	private function setDashboard( ContainerBuilder $container, $config ) {
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

	private function loadUser(array $config, ContainerBuilder $container, YamlFileLoader $loader)
	{
		$loader->load('user.yaml');

		$this->remapParametersNamespaces($config, $container, array(
			'form' => 'octopouce.admin.user.form.%s',
		));

	}

	/**
	 * @param array            $config
	 * @param ContainerBuilder $container
	 * @param array            $map
	 */
	protected function remapParameters(array $config, ContainerBuilder $container, array $map)
	{
		foreach ($map as $name => $paramName) {
			if (array_key_exists($name, $config)) {
				$container->setParameter($paramName, $config[$name]);
			}
		}
	}
	/**
	 * @param array            $config
	 * @param ContainerBuilder $container
	 * @param array            $namespaces
	 */
	protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
	{
		foreach ($namespaces as $ns => $map) {
			if ($ns) {
				if (!array_key_exists($ns, $config)) {
					continue;
				}
				$namespaceConfig = $config[$ns];
			} else {
				$namespaceConfig = $config;
			}
			if (is_array($map)) {
				$this->remapParameters($namespaceConfig, $container, $map);
			} else {
				foreach ($namespaceConfig as $name => $value) {
					$container->setParameter(sprintf($map, $name), $value);
				}
			}
		}
	}
}
