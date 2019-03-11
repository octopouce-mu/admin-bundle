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
     * @IsGranted("ROLE_ADMIN")
     */
    public function index( DashboardService $dashboardService, Request $request ): Response
    {
    	 $dashboardService->clearCache($request->get('reset_stats'));

		$stats = $dashboardService->getData();

		if($stats === false) {
			return $this->redirectToRoute('octopouce_admin_user_index');
		}

        return $this->render('@OctopouceAdmin/Dashboard/index.html.twig', [
	        'stats' => $stats
        ]);
    }

	/**
	 * @Route("/", name="octopouce_admin_dashboard_clear_cache")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function clearCache( DashboardService $dashboardService, Request $request ): Response
	{
		$dashboardService->clearCache($request->get('reset_stats'));

		$stats = $dashboardService->getData();

		if($stats === false) {
			return $this->redirectToRoute('octopouce_admin_user_index');
		}

		return $this->render('@OctopouceAdmin/Dashboard/index.html.twig', [
			'stats' => $stats
		]);
	}
}
