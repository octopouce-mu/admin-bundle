<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 08/05/2018
 */

namespace Octopouce\AdminBundle\Utils\User;


class TokenGenerator {

	public function generateToken()
	{
		return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
	}
}