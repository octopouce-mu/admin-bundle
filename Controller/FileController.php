<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/08/2018
 */

namespace Octopouce\AdminBundle\Controller;

use Octopouce\AdminBundle\Utils\FileUploader;
use Octopouce\AdminBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @Route("/file")
 * @IsGranted("ROLE_USER")
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
			$now = new \DateTime();
			$path = 'uploads/'.$now->format('Y/m');
			$file = $em->getRepository(File::class)->findOneByPath($path.'/'.$request->files->get('file')->getClientOriginalName());
			if(!$file) {
				if($request->request->has('title')) {

					$name = $fileUploader->upload($request->files->get('file'), 'date');

					$file = new File();
					$file->setTitle($request->request->get('title'));
					$file->setAlt($request->request->get('alt'));

					$file->setPath($name);
					$em->persist($file);
					$em->flush();
				} else {
					return new JsonResponse('Field title missing', 500);
				}
			} else {
				return new JsonResponse('File with same name already exist', 500);
			}
		} else {
			return new JsonResponse('Field file missing', 500);
		}

		return new JsonResponse(['id' => $file->getId(), 'path' => $file->getPath(), 'title' => $file->getTitle(), 'alt' => $file->getAlt()]);
	}

	/**
	 * @Route("/api/wysiwyg", name="octopouce_admin_admin_file_api_wysiwyg", options={"expose"=true})
	 * @Method("POST")
	 */
	public function apiWysiwyg(Request $request, FileUploader $fileUploader) : Response {

		if($request->files->has('upload') && $request->files->get('upload')) {
			$name = $fileUploader->upload($request->files->get('upload'), 'date');
		} else {
			return new JsonResponse('Field file missing', 500);
		}

		return new JsonResponse($request->headers->get('origin').'/'.$name);
	}

	/**
	 * @Route("/api/{id}", name="octopouce_admin_admin_file_api_get", options={"expose"=true})
	 */
	public function apiGet(File $file) : Response {

		if($file->getPath() instanceof \Symfony\Component\HttpFoundation\File\File) {
			return new JsonResponse(['id' => $file->getId(), 'path' => $file->getPath()->getPathName(), 'alt' => $file->getAlt(), 'title' => $file->getTitle()]);
		} else {
			return new JsonResponse(['id' => $file->getId(), 'path' => $file->getPath(), 'alt' => $file->getAlt(), 'title' => $file->getTitle()]);
		}
	}
}