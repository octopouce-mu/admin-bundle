<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 08/05/2018
 */

namespace Octopouce\AdminBundle\Utils\User;


use App\Entity\Account\User;
use Octopouce\AdminBundle\Utils\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResettingPassword {

	/**
	 * @var TokenGenerator $tokenGenerator
	 */
	private $tokenGenerator;

	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * @var MailerService
	 */
	private $mailer;

	/**
	 * @var UrlGeneratorInterface
	 */
	protected $router;

	/**
	 * @var \Twig_Environment
	 */
	protected $twig;

	/**
	 * ResettingService constructor.
	 *
	 * @param TokenGenerator $tokenGenerator
	 * @param EntityManagerInterface $em
	 * @param MailerService $mailer
	 * @param UrlGeneratorInterface $router
	 * @param \Twig_Environment $twig
	 */
	public function __construct( TokenGenerator $tokenGenerator, EntityManagerInterface $em, MailerService $mailer, UrlGeneratorInterface $router, \Twig_Environment $twig ) {
		$this->tokenGenerator = $tokenGenerator;
		$this->em             = $em;
		$this->mailer         = $mailer;
		$this->router         = $router;
		$this->twig           = $twig;
	}


	public function request(User $user){
		$token = $this->tokenGenerator->generateToken();

		$user->setResetToken($token);
		$user->setPasswordRequestedAt(new \DateTime());

		$this->sendMailRequest($user);

		$this->em->flush();
	}

	private function sendMailRequest(User $user){

		$url = $this->router->generate('octopouce_octopouce_password_resetting_reset', array('token' => $user->getResetToken()), UrlGeneratorInterface::ABSOLUTE_URL);
		$rendered = $this->twig->render('@OctopouceAdmin/Security/Resetting/request-mail.html.twig', array(
			'user' => $user,
			'confirmationUrl' => $url,
		));

		$this->mailer->send($user->getEmail(), 'Forgot password', $rendered);
	}
}