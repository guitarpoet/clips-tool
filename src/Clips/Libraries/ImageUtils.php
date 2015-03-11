<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Interfaces\Initializable;

/**
 * The image process utils, supports php imagick and php gd.
 *
 * @author Jack
 * @date Sat Feb 21 18:39:58 2015
 *
 */
class ImageUtils implements Initializable {

	public function init() {
		if (\extension_loaded('imagick')) {
			$this->type = 'imagick';
		}
		else if (\extension_loaded('gd')) {
			$this->type = 'gd';
		}
		\Clips\log('Using php extension {0} as image processing library.', array($this->type));
	}

	public function create($img) {
		return $this->{$this->type.'Create'}($img);
	}

	public function size($img) {
		return $this->{$this->type.'Size'}($img);
	}

	public function thumbnail($from, $to, $width, $height = 0) {
		return $this->{$this->type.'Thumbnail'}($from, $to, $width, $height);
	}

	public function imagickCreate($file) {
		if(\file_exists($file)) {
			$img = new \Imagick($file);
			$img->setResourceLimit(6, 1); // Set the thread limit to image magick
			return $img; 
		}
		return false;
	}

	public function imagickSize($file) {
		$img = $this->imagickCreate($file);
		if($img)
			return $img->getImageGeometry(); 
		return false;
	}

	public function imagickThumbnail($from, $to, $width, $height = 0) {
		$img = $this->imagickCreate($from);
		if($img) {
			$img->thumbnailImage($width, $height);
			$img->writeImage($to);
			return $to;
		}
		return false;
	}

	public function gdCreate($img) {
		if(\file_exists($img)) {
			$path_parts = \pathinfo($img);
			$ext = $path_parts['extension'];
			if($ext == 'jpg' || $ext == 'jpeg')
				$src_img = \imagecreatefromjpeg($img);
			else
				$src_img = \imagecreatefrompng($img);

			return $src_img;
		}
		return false;
	}

	public function gdSize($file) {
		$img = $this->gdCreate($file);
		if($img) {
			return array('width' => \imageSX($img), 'height' => \imageSY($img));
		}
		return false;
	}

	public function gdThumbnail($from, $to, $width, $height = 0) {
		$src_img = $this->gdCreate($from);
		if($src_img) {
			$old_x=\imageSX($src_img);
			$old_y=\imageSY($src_img);
		
			if($height == 0) {
				$height = $old_y * $width / $old_x;
			}

			$dst_img=\ImageCreateTrueColor($width,$height);
			\imagecopyresampled($dst_img,$src_img,0,0,0,0,$width,$height,$old_x,$old_y);

			$path_parts = \pathinfo($to);
			$ext = $path_parts['extension'];

			if($ext == 'jpg' || $ext == 'jpeg')
				\imagejpeg($dst_img, $to);
			else
				\imagepng($dst_img, $to);

			\imagedestroy($dst_img);
			\imagedestroy($src_img);
		}
	}
}
