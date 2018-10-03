<?php

namespace Octopouce\AdminBundle\DataFixtures;

use App\Entity\Account\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
	    $this->loadUsers($manager);
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles, $createdAt]) {
	        if(!$manager->getRepository(User::class)->findOneByEmail($email)) {
		        $user = new User();
		        $user->setFirstname( $fullname );
		        $user->setUsername( $username );
		        $user->setPassword( $this->passwordEncoder->encodePassword( $user, $password ) );
		        $user->setEmail( $email );
		        $user->setRoles( $roles );
		        $user->setCreatedAt( $createdAt );

		        $manager->persist( $user );
	        }
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
    	$now = new \DateTime();
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
	        ['Kevin', 'kevin', 'octopouce', 'kevin@octopouce.mu', ['ROLE_SUPER_ADMIN'], $now],
	        ['Octopouce', 'octopouce', '$Octopouce2018', 'contact@octopouce.mu', ['ROLE_ADMIN'], $now],
	        ['Demo', 'demo', 'demo', 'demo@octopouce.mu', ['ROLE_USER'], $now],
        ];
    }
}
