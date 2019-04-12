<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 26/03/2018
 */

namespace Octopouce\AdminBundle\Service\Transformer;


use Octopouce\AdminBundle\Entity\Option;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class OptionTransformer {

	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * @var FilesystemAdapter
	 */
	private $cache;

	/**
	 * OptionTransformer constructor.
	 *
	 * @param EntityManagerInterface $em
	 */
	public function __construct( EntityManagerInterface $em) {
		$this->em = $em;
		$this->cache = new FilesystemAdapter();
	}

	public function getOptionsWithKeyName(){
		$cachedOptions = $this->cache->getItem('thor.options');
//		if(!$cachedOptions->isHit() || !$cachedOptions->get()){
			$options = $this->em->getRepository(Option::class)->findAll();
			$opts = [];
			foreach ($options as $option){
				$opts[$option->getName()] = $option;
			}
			$this->cache->save($cachedOptions->set($opts));
//		}else{
//			$opts = $cachedOptions->get();
//		}

		return $opts;
	}


}
