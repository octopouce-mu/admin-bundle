<?php

namespace Octopouce\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="octopouce_admin_login_admin")
     */
    public function loginAdmin(AuthenticationUtils $helper): Response
    {
	    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');

        return $this->render('@OctopouceAdmin/Security/login.html.twig', [
	        'lastUsername' => $helper->getLastUsername(),
	        'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="octopouce_admin_logout")
     */
    public function logout(): void
    {
	    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        throw new \Exception('This should never be reached!');
    }
}
