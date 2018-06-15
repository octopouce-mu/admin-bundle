<?php

namespace Octopouce\AdminBundle\Controller;

use App\Entity\Account\User;
use App\Form\Security\Password\RequestType;
use App\Form\Security\Password\ResetType;
use Octopouce\AdminBundle\Utils\User\ResettingPassword;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class PasswordController extends AbstractController
{

    /**
     * @Route("/forgot-password", name="octopouce_admin_password_resetting")
     */
    public function resetting(Request $request, ResettingPassword $resettingPassword): Response
    {
    	$user = new User();
    	$form = $this->createForm(RequestType::class, $user);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid()){
		    $em = $this->getDoctrine()->getManager();
    		$user = $em->getRepository(User::class)->findOneByEmail($user->getEmail());
    		if($user){
			    $resettingPassword->request($user);

			    $this->addFlash('success', 'Get your mails for to change your password');
			    return $this->redirectToRoute('octopouce_admin_login_admin');

		    }else{
			    $this->addFlash('error', 'User isn\'t exist.');
		    }
	    }

        return $this->render('@OctopouceAdmin/Security/Resetting/request.html.twig', [
        	'form' => $form->createView()
        ]);
    }

	/**
	 * @Route("/forgot-password/reset", name="octopouce_admin_password_resetting_reset")
	 */
	public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
	{
		$em = $this->getDoctrine()->getManager();
		$token = $request->get('token');
		if(!$token){
			$this->addFlash('error', 'Wrong token!');
			return $this->redirectToRoute('octopouce_admin_login_admin');
		}

		$user = $em->getRepository(User::class)->findOneByResetToken($token);
		if(!$user){
			$this->addFlash('error', 'Wrong token!');
			return $this->redirectToRoute('octopouce_admin_login_admin');
		}

		$now = new \DateTime();
		if($now->modify('+1 hour') < $user->getResetDate()){
			$this->addFlash('error', 'The token is no longer valid!');
			return $this->redirectToRoute('octopouce_admin_password_resetting');
		}

		$form = $this->createForm(ResetType::class, $user);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()){

			$plainPassword = $form->get('password')->getData();
			if ($plainPassword)  {
				$user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));
				$user->setResetToken(null);
				$em->flush();

				$this->addFlash('success', 'Password changed');
			}else{
				$this->addFlash('error', 'Password is empty!');
			}

			return $this->redirectToRoute('octopouce_admin_login_admin');
		}

		return $this->render('@OctopouceAdmin/Security/Resetting/reset.html.twig', [
			'form' => $form->createView(),
			'token' => $token
		]);
	}



}
