<?php

namespace Octopouce\AdminBundle\Controller;

use Octopouce\AdminBundle\Entity\Category;
use Octopouce\AdminBundle\Entity\Option;
use Octopouce\AdminBundle\Form\OptionType;
use Octopouce\AdminBundle\Service\DashboardService;
use Octopouce\AdminBundle\Utils\FileUploader;
use Octopouce\AdminBundle\Utils\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/option")
 * @IsGranted("ROLE_USER")
 */
class OptionController extends AbstractController
{
    /**
     * @Route("/", name="octopouce_admin_option_index")
     */
    public function index(Request $request, FileUploader $fileUploader, DashboardService $dashboardService, MailerService $mailerService, AuthorizationCheckerInterface $authChecker): Response
    {

    	$em = $this->getDoctrine()->getManager();

    	// get category option page
    	$categoryName = $request->get('category') ? $request->get('category') : 'general';

    	$category = $em->getRepository(Category::class)->findOneBy(['name' => $categoryName, 'type' => 'option']);
    	if(!$category || $category->getOptions()->count() == 0){
		    $category = $em->getRepository(Category::class)->findOneBy(['name' => 'general', 'type' => 'option']);
	    }

	    if ( ($category->getType() == 'blog' && false === $authChecker->isGranted('ROLE_BLOG')) || false === $authChecker->isGranted('ROLE_ADMIN') ) {
		    throw new AccessDeniedException('Unable to access this page!');
	    }

		$form = $this->createForm(OptionType::class, null, [
			'options' => $category->getOptions(),
			'category' => $category
		]);

	    $form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()) {

	    	$categories = []; // array for remove cache

	    	foreach ($form->getData() as $category => $options){
	    		foreach ($options as $id => $value){
				    $option = $em->getRepository(Option::class)->find($id);
				    if($option){
					    // if the option is file
					    if(is_uploaded_file($value)){
						    $value = $fileUploader->upload($value, ($option->getName() == 'GOOGLE_GA_JSON') ? '../var' : null);
					    }

					    if($option->getName() == 'GOOGLE_GA_JSON' && !$value){
					    	continue;
					    }

					    $option->setValue($value);

					    // insert category name in array for remove cache
					    if(!in_array($option->getCategory()->getName(), $categories))
						    $categories[] = $option->getCategory()->getName();
				    }
			    }
		    }
		    $em->flush();

	    	// remove cache
	    	if($categories){
	    		foreach ($categories as $category){
				    $dashboardService->clearCache('stats.'.$category);
			    }
		    }

		    $cache = new FilesystemAdapter();
	    	$cache->deleteItem('thor.options');


		    if($form->has('submit_send') && $form->get('submit_send')->isClicked()){
			    $mailerService->send();
		    }
	    }

        return $this->render('@OctopouceAdmin/Option/index.html.twig', [
	        'form' => $form->createView(),
	        'category' => $categoryName
        ]);
    }
}
