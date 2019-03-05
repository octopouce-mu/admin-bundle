<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-02-08
 */

namespace Octopouce\AdminBundle\Security;

use App\Entity\Account\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
	public function checkPreAuth(UserInterface $user)
	{
		if (!$user instanceof User) {
			return;
		}
	}

	public function checkPostAuth(UserInterface $user)
	{
		if (!$user instanceof User) {
			return;
		}

		if (!$user->isEnabled()) {
			throw new CustomUserMessageAuthenticationException(
				'Your account was disabled. Sorry about that! Check your e-mail for to confirm your account or contact an administrator!'
			);
		}
	}
}
