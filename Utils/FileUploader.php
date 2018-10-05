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
		if (!$path && !file_exists($this->getTargetDirectory())) {
			mkdir($this->getTargetDirectory(), 0777, true);
		}

		if($name) {
			$name = $name.'.'.$file->getClientOriginalExtension();
		} else {
			$name = $file->getClientOriginalName();
		}

		$name = $this->slugify($name);

		if(file_exists($path.'/'.$name)){
			$name = '2_'.$name;
		}

		$file->move($path ? $path : $this->getTargetDirectory(), $name);

		return $name;
	}

	public function setTargetDirectory($targetDirectory)
	{
		$this->targetDirectory = $targetDirectory;
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}

	private function slugify($text)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d.]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w.]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			throw new \Exception('Error slugify');
		}

		return $text;
	}

}