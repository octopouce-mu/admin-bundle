<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 22/03/2018
 */

namespace Octopouce\AdminBundle\Utils;


use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
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
			mkdir($this->getTargetDirectory(), 0775, true);
		}

		if($path && $path == 'date') {
			$now = new \DateTime();
			$path = 'uploads/'.$now->format('Y/m');

			$fileSystem = new Filesystem();
			$fileSystem->mkdir($path, 0775);
		}

		if($name) {
			$name = $name.'.'.$file->getClientOriginalExtension();
		} else {
			$name = $file->getClientOriginalName();
		}

		$name = $this->slugify($name);

		if(file_exists($path.'/'.$name)){

			$exist = true;
			$i = 2;

			while ($exist) {
				$p = $path.'/'.$i.'_'.$name;
				if(file_exists($p)) {
					$i++;
				} else {
					$exist = false;
					$name = $i.'_'.$name;
				}
			}
		}

		$file->move($path ? $path : $this->getTargetDirectory(), $name);

		return $path.'/'.$name;
	}

	public function uploadWithCollection($files, $name, $old = null) {
		$fileSystem = new Filesystem();

		if($old) { // update
			if(!is_array($files) && !$files instanceof PersistentCollection) {
				if($files) {
					if($old instanceof File) $fileSystem->remove($old);

					$nameHeaderImage = $this->upload($files, 'date', $name);
					return $nameHeaderImage;
				} else {
					return $old instanceof File ? $old->getPathName() : $old;
				}
			} else {
				foreach ($files as $k => $file) {

					if($file->getPath()) {
						if($file->getPath() instanceof File){
							$nameImage = $this->upload($file->getPath(), 'date');
							$file->setPath($nameImage);
						}

					} else {
						if(array_key_exists($file->getId(), $old)) {
							$oldFile = $old[$file->getId()];
							$file->setPath($oldFile->getPath()->getPathName());
						}
					}
					unset($old[$file->getId()]);
				}
				return $old;
			}

		} else { // create
			if(!is_array($files) && !$files instanceof PersistentCollection) {
				if($files) {
					$nameThumbnail = $this->upload( $files, 'date', $name);
					return $nameThumbnail;
				}
			} else {
				foreach ($files as $k => $file) {
					$nameImage = $this->upload($file->getPath(), 'date');
					$file->setPath($nameImage);
				}
			}
		}
		return null;
	}

	public function removeFiles($files) {
		$fileSystem = new Filesystem();
		if($files instanceof File) {
			$fileSystem->remove($files->getPathName());
		} elseif(is_array($files) || $files instanceof PersistentCollection) {
			foreach ($files as $file) {
				if($file->getPath() instanceof File) {
					$fileSystem->remove($file->getPath()->getPathName());
				}
			}
		}
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