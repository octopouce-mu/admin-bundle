<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/11/2018
 */

namespace Octopouce\AdminBundle\Controller;

use Octopouce\AdminBundle\Service\Featured\FeaturedORMHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/featured")
 * @IsGranted("ROLE_ADMIN")
 */
class FeaturedController extends AbstractController {

	/**
	 * @Route("/", name="octopouce_admin_admin_featured_action", options={"expose"=true})
	 * @Method("POST")
	 */
	public function action(Request $request, FeaturedORMHandler $featuredService) {

		$entity = $request->request->get('class');
		$entity = str_replace('/', '\\', $entity);

		$setter = 'featured';

		$id = (int) $request->request->get('id');
		$featured = (int) $request->request->get('featured');

		$updateDb = $featuredService->setFeatured($entity, $setter, $id, $featured);

		if($updateDb === true){
			return new Response($featured);
		}else{
			return new Response('Error', 500);
		}
	}
}