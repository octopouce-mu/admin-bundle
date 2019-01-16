<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 08/11/2017
 */

namespace Octopouce\AdminBundle\Utils;


use Doctrine\ORM\EntityManagerInterface;

class CommonFunction {

	private $em;

	public function __construct(EntityManagerInterface $em) {
		$this->em = $em;
	}


	public function setMethod( $key, $object, $value ) {
		$currMethod = 'set';
		$key = ucfirst($key);
		$underscore = strpos($key, '_');
		if($underscore){
			$explode = explode('_', $key);
			foreach ($explode as $elem){
				$currMethod .= ucfirst($elem);
			}
		}else{
			$currMethod .= $key;
		}
		if (method_exists($object, $currMethod) && $value !== null) {
			$object->$currMethod($value);
		}
		return $object;
	}

	public function slugify($text)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}

	public function getSlugLocales($slug, $entity) {
		$localeDefault = $request->getDefaultLocale();
		$locale = $request->getLocale();

		if($localeDefault == $locale) {
			$page = $this->getDoctrine()->getRepository(Page::class)->findOneBySlug($slug);
		} else {
			$pageTranslation = $this->getDoctrine()->getRepository(Page::getTranslationEntityClass())->findOneBySlug($slug);
			if(!$pageTranslation) {
				throw new NotFoundHttpException("No route found");
			}
			$page = $pageTranslation->getTranslatable();
		}

		if(!$page) {
			throw new NotFoundHttpException("No route found");
		}
	}

}
