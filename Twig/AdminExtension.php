<?php

namespace Octopouce\AdminBundle\Twig;

use App\Entity\Account\User;
use Octopouce\AdminBundle\Service\Transformer\OptionTransformer;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AdminExtension extends AbstractExtension implements \Twig_Extension_GlobalsInterface
{
	private $options;
	private $router;

	private $dashboardEnabled;


	public function __construct(OptionTransformer $optionTransformer, RouterInterface $router)
	{
		$this->options = $optionTransformer->getOptionsWithKeyName();
		$this->router = $router;
	}

	public function setDashboardEnabled( $dashboardEnabled )
	{
		$this->dashboardEnabled = $dashboardEnabled;
	}

	public function getGlobals()
	{
		$globals = [];
		$globals['thor'] = [
			'options' => $this->options,
			'dashboard' => [
				'enabled' => $this->dashboardEnabled
			]
		];

		return $globals;
	}

	public function getFilters(): array
	{
		return [
			new TwigFilter('cast_to_array', [$this, 'objectFilter']),
			new TwigFilter('getClass', [$this, 'getClass']),
			new TwigFilter('routeExist', [$this, 'routeExist']),
		];
	}

	public function getClass($object)
	{
		$class = get_class($object);
		$class = str_replace('Proxies\__CG__\\', '', $class);

		$class = str_replace('\\', '/', $class);


		return $class;
	}

	public function objectFilter($stdClassObject) {
		$class = get_class($stdClassObject);
		$class = str_replace('Proxies\__CG__\\', '', $class);

		// Just typecast it to an array
		$response = (array)$stdClassObject;
		foreach ($response as $key => $val){
			$newKey = preg_replace('/[\x00-\x1F\x7F*]/', '', $key);
			$newKey = str_replace([$class], '', $newKey);

			if(!in_array($newKey, ['translations', 'newTranslations', 'newTranslations', 'defaultLocale', 'currentLocale', 'translatable', 'locale'])){
				$response[$newKey] = $val;
			}

			unset($response[$key]);
		}


		return $response;
	}

	public function routeExist($name){
		return (null === $this->router->getRouteCollection()->get($name)) ? false : true;
	}
}
