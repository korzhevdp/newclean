<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uploadmodel extends CI_Model {

	private function createImageContainer($file, $type) {
		if (strtolower($type) === "image/jpeg") {
			return imagecreatefromjpeg($file);
		}
		if (strtolower($type) === "image/png") {
			return imagecreatefrompng($file);
		}
		if (strtolower($type) === "image/gif") {
			return imagecreatefromgif($file);
		}
		return false;
	}

	private function resizeImage($image, $width, $locType, $folder) {
		$outfile = "uploads/img/".uniqid().'.jpg';

		if ($image) {
			$size     = array( imagesx($image), imagesy($image) );
			$height   = round($size[1] * ($width / $size[0]));
			$new = ImageCreateTrueColor($width, $height);
			ImageCopyResampled($new, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			imageJpeg($new, $outfile, 70);
			imageDestroy($new);
		}
		return $outfile;
	}

	private function check_directories($type, $folder) {
		//print "<br>\n\n".$type."  -- ".$folder."<br>\n\n";
		if ( !file_exists('../'.$type.'/'.$folder) ) {
			mkdir('../'.$type.'/'.$folder, 0775);
		}
		if ( !file_exists('../'.$type.'/'.$folder.'/small') ) {
			mkdir('../'.$type.'/'.$folder.'/small', 0775);
		}
		if ( !file_exists('../'.$type.'/'.$folder.'/mid') ) {
			mkdir('../'.$type.'/'.$folder.'/mid', 0775);
		}
	}
	
	public function accomodateFile($file) {
		//на случай если в будущем будем раскладывать файлики по папкам
		//$this->check_directories($file['type'], $file['folder']);
		$image = $this->createImageContainer($file['tmp_name'], $file['type']);
		//resizeImage($image, '800', $file['type'], $_REQUEST['folder']);
		$filename = $this->resizeImage($image, '600', $file['type'], "");
		//resizeImage($image, '50',  $file['type']);
		imageDestroy($image);
		unlink($file['tmp_name']);
		return $filename;
	}
}