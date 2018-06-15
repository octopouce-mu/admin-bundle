<?php

namespace Octopouce\AdminBundle\DataFixtures;

use Octopouce\AdminBundle\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
	    foreach ($this->getData() as [$name, $parent]) {
		    $category = new Category();
		    $category->setName($name);
		    if($parent){
		    	$category->setParent($this->getReference('cat-'.$parent));
		    }
		    $this->addReference('cat-'.$name, $category);


		    $manager->persist($category);
	    }

	    $manager->flush();
    }


	private function getData(): array
	{
		return [
			// $optionData = [$name, $value];
			['general', null],

			['social_media', null],
			['facebook', 'social_media'],
			['twitter', 'social_media'],
			['youtube', 'social_media'],
			['google', 'social_media'],
			['instagram', 'social_media'],

			['mail', 'general'],
			['company', 'general'],
		];
	}
}
