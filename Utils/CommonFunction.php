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


}
