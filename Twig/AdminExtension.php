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
	private $tokenStorage;
	private $router;


	public function __construct(OptionTransformer $optionTransformer, TokenStorageInterface $tokenStorage, RouterInterface $router)
	{
		$this->options = $optionTransformer->getOptionsWithKeyName();
		$this->tokenStorage = $tokenStorage;
		$this->router = $router;
	}

	public function getGlobals()
	{
		$globals = [];
		$globals['thor'] = ['options' => $this->options];

		$token = $this->tokenStorage->getToken();
		if($token && is_object($token->getUser()) && get_class($token->getUser()) == User::class){
			$subscriptions = $token->getUser()->getSubscriptionsActive();
//			if(count($subscriptions) > 1)
			$globals['subscriptionsActive'] = $subscriptions;
		}

		return $globals;
	}

	public function getFilters(): array
	{
		return [
			new TwigFilter('cast_to_array', [$this, 'objectFilter']),
			new TwigFilter('routeExist', [$this, 'routeExist']),
		];
	}

	public function objectFilter($stdClassObject) {
		$class = get_class($stdClassObject);
		// Just typecast it to an array
		$response = (array)$stdClassObject;
		foreach ($response as $key => $val){
			$newKey = preg_replace('/[\x00-\x1F\x7F*]/', '', $key);
			$newKey = str_replace([$class], '', $newKey);

			if(!in_array($newKey, ['translations', 'newTranslations', 'newTranslations', 'defaultLocale', 'currentLocale', 'translatable', 'locale'])){
				$response[$newKey] = $val;
//				unset($response[$newKey]);
			}

			unset($response[$key]);
		}

//		var_dump($response);die;

		return $response;
	}

	public function routeExist($name){
		return (null === $this->router->getRouteCollection()->get($name)) ? false : true;
	}
}
