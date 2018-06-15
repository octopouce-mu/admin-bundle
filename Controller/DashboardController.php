<?php


namespace Octopouce\AdminBundle\Controller;

use Octopouce\AdminBundle\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="octopouce_admin_dashboard_index")
     */
    public function index( DashboardService $dashboardService, Request $request ): Response
    {
    	if($request->get('reset_stats')) $dashboardService->clearCache($request->get('reset_stats'));

		$stats = $dashboardService->getData();

        return $this->render('@OctopouceAdmin/Dashboard/index.html.twig', [
	        'stats' => $stats
        ]);
    }
}
