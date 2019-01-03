<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 06/06/2018
 */

namespace Octopouce\AdminBundle\EventListener;

use Octopouce\AdminBundle\Entity\File;
use Symfony\Component\HttpFoundation\File\File as FileType;
use Doctrine\ORM\Event\LifecycleEventArgs;

class FileListener {

	public function postLoad(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if (!$entity instanceof File && class_exists('Proxies\__CG__\Octopouce\AdminBundle\Entity\File') && !$entity instanceof Proxies\__CG__\Octopouce\AdminBundle\Entity\File ) {
			return;
		}

		$image = $entity->getPath();
		if ($image && file_exists($image)) {
			$entity->setPath(new FileType($image));
		} else{
			$entity->setPath(null);
		}
	}
}