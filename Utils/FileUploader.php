<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 22/03/2018
 */

namespace Octopouce\AdminBundle\Utils;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader {
	private $targetDirectory;

	public function __construct($targetDirectory)
	{
		$this->targetDirectory = $targetDirectory;
	}

	public function upload(UploadedFile $file, $path = null)
	{
		$file->move($path ? $path : $this->getTargetDirectory(), $file->getClientOriginalName());

		return $file->getClientOriginalName();
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}
}