<?php


namespace Octopouce\AdminBundle\Controller;

use App\Entity\Account\User;
use Octopouce\AdminBundle\Form\Factory\FactoryInterface;
use Octopouce\AdminBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/user")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
	private $formFactory;

	public function __construct(FactoryInterface $formFactory)
	{
		$this->formFactory = $formFactory;
	}

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
	 * @Route("/create", name="octopouce_admin_user_create")
	 * @IsGranted("ROLE_SUPER_ADMIN")
	 */
	public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
	{
		$user = new User();

		$form = $this->createForm(UserType::class, $user, [
			'super_admin' => true
		]);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$plainPassword = $form->get('password')->getData();
			if ($plainPassword)  {
				$user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));
			}
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			return $this->redirectToRoute('octopouce_admin_user_index');
		}

		return $this->render('@OctopouceAdmin/User/create.html.twig', [
			'user' => $user,
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/{id}", name="octopouce_admin_user_show")
	 */
	public function show($id): Response
	{
		$user = $this->getDoctrine()->getRepository(User::class)->find($id);

		return $this->render('@OctopouceAdmin/User/show.html.twig', [
			'user' => $user,
		]);
	}



	/**
	 * @Route("/edit/{id}", name="octopouce_admin_user_edit")
	 */
	public function edit(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder, AuthorizationCheckerInterface $authChecker): Response
	{
		$userIdentified = $this->getUser();
		$user = $this->getDoctrine()->getRepository(User::class)->find($id);

		if (false === $authChecker->isGranted('ROLE_SUPER_ADMIN') && $userIdentified !== $user) {
			throw new AccessDeniedException('Unable to access this page!');
		}


		$form = $this->createForm(UserType::class, $user, [
			'edit' => true,
			'super_admin' => $authChecker->isGranted('ROLE_SUPER_ADMIN')
		]);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$plainPassword = $form->get('password')->getData();
			if ($plainPassword)  {
				$user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));
			}
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('octopouce_admin_user_index');
		}

		return $this->render('@OctopouceAdmin/User/edit.html.twig', [
			'user' => $user,
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/delete/{id}", name="octopouce_admin_user_delete")
	 */
	public function delete($id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository(User::class)->find($id);

		if($user != $this->getUser()){
			$em->remove($user);
			$em->flush();
		}

		return $this->redirectToRoute('octopouce_admin_user_index');
	}
}
