<?php


namespace Octopouce\AdminBundle\Controller;

use App\Entity\Account\User;
use Octopouce\AdminBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="octopouce_admin_user_index")
     */
    public function index(): Response
    {
    	$users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('@OctopouceAdmin/User/index.html.twig', [
	        'users' => $users
        ]);
    }

	/**
	 * @Route("/{id}", name="octopouce_admin_user_show")
	 */
	public function show(User $user): Response
	{
		return $this->render('@OctopouceAdmin/User/show.html.twig', [
			'user' => $user,
		]);
	}

	/**
	 * @Route("/edit/{id}", name="octopouce_admin_user_edit")
	 */
	public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
	{
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$plainPassword = $form->get('password')->getData();
			if ($plainPassword)  {
				$user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));
			}
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('octopouce_admin_user_index');
		}

		return $this->render('@OctopouceAdmin/Crud/edit.html.twig', [
			'user' => $user,
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/delete/{id}", name="octopouce_admin_user_delete")
	 */
	public function delete(User $user): Response
	{
		$em = $this->getDoctrine()->getManager();
		if($user != $this->getUser()){
			$em->remove($user);
			$em->flush();
		}

		return $this->redirectToRoute('octopouce_admin_user_index');
	}
}
