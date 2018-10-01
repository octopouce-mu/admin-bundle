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

	public function upload(UploadedFile $file, $path = null, $name = null)
	{
		if (!file_exists($this->getTargetDirectory())) {
			mkdir($this->getTargetDirectory(), 0777, true);
		}

		$file->move($path ? $path : $this->getTargetDirectory(), $name ? $name : $file->getClientOriginalName());

		return $file->getClientOriginalName();
	}

	public function setTargetDirectory($targetDirectory)
	{
		$this->targetDirectory = $targetDirectory;
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}


}