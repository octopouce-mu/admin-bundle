<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 08/11/2017
 */

namespace Octopouce\AdminBundle\Utils;


class CommonFunction {

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

}
