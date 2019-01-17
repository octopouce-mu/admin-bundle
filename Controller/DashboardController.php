<?php


namespace Octopouce\AdminBundle\Controller;

use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;
use Octopouce\AdminBundle\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="octopouce_admin_dashboard_index")
     * @IsGranted("ROLE_USER")
     */
    public function index( DashboardService $dashboardService, Request $request ): Response
    {
    	if($request->get('reset_stats')) $dashboardService->clearCache($request->get('reset_stats'));

		$stats = $dashboardService->getData();

		if($stats === false) {
			return $this->redirectToRoute('octopouce_admin_user_index');
		}

        return $this->render('@OctopouceAdmin/Dashboard/index.html.twig', [
	        'stats' => $stats
        ]);
    }
}
