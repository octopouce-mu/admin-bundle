<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/11/2018
 */

namespace Octopouce\AdminBundle\Controller;

use Octopouce\AdminBundle\Service\Sortable\PositionORMHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortable")
 * @IsGranted("ROLE_ADMIN")
 */
class SortableController extends AbstractController {

	/**
	 * @Route("/", name="octopouce_admin_admin_sortable_drag", options={"expose"=true})
	 * @Method("POST")
	 */
	public function drag(Request $request, PositionORMHandler $positionService) {

		$entity = $request->request->get('class');
		$entity = str_replace('/', '\\', $entity);

		$setter = 'sort';

		$dataPositionList = $request->request->get('data');

		$updateDb = $positionService->setPosition($entity, $setter, $dataPositionList);

		if($updateDb === true){
			return new Response('Success');
		}else{
			return new Response('Error', 500);
		}
	}
}