<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/08/2018
 */

namespace Octopouce\AdminBundle\Controller;

use Octopouce\AdminBundle\Utils\FileUploader;
use Octopouce\AdminBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/file")
 */
class FileController extends Controller
{
	/**
	 * @Route("/api/create", name="octopouce_admin_admin_file_api_create", options={"expose"=true})
	 * @Method("POST")
	 */
	public function apiCreate(Request $request, FileUploader $fileUploader) : Response {
		$em = $this->getDoctrine()->getManager();

		if($request->files->has('file') && $request->files->get('file')) {
			$name = $fileUploader->upload($request->files->get('file'), 'uploads/gallery');


			$file = $em->getRepository(File::class)->findOneByPath('uploads/gallery/'.$name);
			if(!$file) {
				if($request->request->has('title')) {
					$file = new File();
					$file->setTitle($request->request->get('title'));

					$file->setPath('uploads/gallery/'.$name);
					$em->persist($file);
					$em->flush();
				} else {
					return new JsonResponse('Field title missing', 500);
				}
			}
		} else {
			return new JsonResponse('Field file missing', 500);
		}

		return new JsonResponse(['id' => $file->getId(), 'path' => $file->getPath()]);
	}

	/**
	 * @Route("/api/{id}", name="octopouce_admin_admin_file_api_get", options={"expose"=true})
	 */
	public function apiGet(File $file) : Response {

		return new JsonResponse(['id' => $file->getId(), 'path' => $file->getPath()]);
	}
}