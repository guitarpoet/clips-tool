<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

/**
 * The same upload library like CodeIgniter
 *
 * @author Jack
 * @date Sat Aug  8 12:15:20 2015
 * @version 1.1
 */
class Upload extends BaseService {
	public $max_size = 0;
	public $max_width = 0;
	public $max_height = 0;
	public $max_filename = 0;
	public $allowed_types = array();
	public $file_temp = "";
	public $file_name = "";
	public $orig_name = "";
	public $file_type = "";
	public $file_size = 0;
	public $file_ext = "";
	public $upload_path = "";
	public $overwrite = false;
	public $encrypt_name = false;
	public $is_image = false;
	public $image_width = 0;
	public $image_height = 0;
	public $image_type = '';
	public $error_msg = array();
	public $mimes = array();
	public $remove_spaces = false;
	public $xss_clean = false;
	public $temp_prefix = 'tmp_';
	public $client_name = '';
	public $field = 'file';

	public function doUpload($props = array()) {
		// Copy the initialize properties
		\Clips\copy_object($props, $this);		
		$field = $this->field;

		if (!isset($_FILES[$field])) {
			$this->addError('No file uploaded!');
			return false;
		}
		
		if(!$this->validate()) {
			return false;
		}

		if (!is_uploaded_file($_FILES[$field]['tmp_name'])) {
			$error = (!isset($_FILES[$field]['error'])) ? 4 : $_FILES[$field]['error'];
			switch($error) {
				case 1:	// UPLOAD_ERR_INI_SIZE
					$this->addError('The file that uploads as %s exceeds the limit!', $field);
					break;
				case 2: // UPLOAD_ERR_FORM_SIZE
					$this->addError('The file that uploads as %s exceeds the limit!', $field);
					break;
				case 3: // UPLOAD_ERR_PARTIAL
					$this->addError('The partial upload file as %s is not allowed!', $field);
					break;
				case 4: // UPLOAD_ERR_NO_FILE
					$this->addError('No file is uploaded for field %s!', $field);
					break;
				case 6: // UPLOAD_ERR_NO_TMP_DIR
					$this->addError('No temp directory is writable!');
					break;
				case 7: // UPLOAD_ERR_CANT_WRITE
					$this->addError('Unable to write the upload file for field %s', $field);
					break;
				case 8: // UPLOAD_ERR_EXTENSION
					$this->addError('Upload stopped caused by the extension for field!', $field);
					break;
				default:
					$this->addError('No file is uploaded for field %s!', $field);
					break;
			}

			return false;
		}

		// Set the uploaded data as class variables
		$this->file_temp = $_FILES[$field]['tmp_name'];
		$this->file_size = $_FILES[$field]['size'];
		$this->file_type = preg_replace("/^(.+?);.*$/", "\\1", $this->file_type);
		$this->file_type = strtolower(trim(stripslashes($this->file_type), '"'));
		$this->file_name = $this->_prep_filename($_FILES[$field]['name']);
		$this->file_ext	 = $this->getExtension($this->file_name);
		$this->client_name = $this->file_name;

		
		// Is the file type allowed to be uploaded?
		if (!$this->validFileType()) {
			$this->addError('Upload file for field %s is not a valid type to upload!', $field);
			return false;
		}

		// Convert the file size to kilobytes
		if ($this->file_size > 0) {
			$this->file_size = round($this->file_size/1024, 2);
		}

		// Is the file size within the allowed maximum?
		if (!$this->validFilesize()) {
			$this->addError('Upload filesize for field %s is too large!', $field);
			return false;
		}

		// Are the image dimensions within the allowed size?
		// Note: This can fail if the server has an open_basdir restriction.
		if (!$this->validDimensions()) {
			$this->addError('The image size of field %s is larger than allowed %s, %s', $this->max_width, $this->max_height);
			return false;
		}

		// Sanitize the file name for security
		$this->file_name = \Clips\clean_file_name($this->file_name);

		// Truncate the file name if it's too long
		if ($this->max_filename > 0) {
			$this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);
		}

		// Remove white spaces in the name
		if ($this->remove_spaces == true) {
			$this->file_name = preg_replace("/\s+/", "_", $this->file_name);
		}
		
		$this->orig_name = $this->file_name;

		if ($this->overwrite == false) {
			$this->file_name = $this->setFilename($this->upload_path, $this->file_name);

			if ($this->file_name === false) {
				return false;
			}
		}

		if (!@copy($this->file_temp, $this->upload_path.$this->file_name)) {
			if (!@move_uploaded_file($this->file_temp, $this->upload_path.$this->file_name)) {
				$this->addError('Failed to upload file of field %s to destination %s', $field, $this->upload_path.$this->file_name);
				return false;
			}
		}
		return true;
	}

	public function setFilename($path, $filename) {
		if ($this->encrypt_name == TRUE) {
			mt_srand();
			$filename = md5(uniqid(mt_rand())).$this->file_ext;
		}

		if (!file_exists($path.$filename)) {
			return $filename;
		}

		$filename = str_replace($this->file_ext, '', $filename);

		$new_filename = '';
		for ($i = 1; $i < 100; $i++) {
			if ( ! file_exists($path.$filename.$i.$this->file_ext)) {
				$new_filename = $filename.$i.$this->file_ext;
				break;
			}
		}

		if ($new_filename == '') {
			$this->addError('No filename when uploading!');
			return false;
		}
		else {
			return $new_filename;
		}
	}

	public function limit_filename_length($filename, $length) {
		if (strlen($filename) < $length) {
			return $filename;
		}

		$ext = '';
		if (strpos($filename, '.') !== false) {
			$parts		= explode('.', $filename);
			$ext		= '.'.array_pop($parts);
			$filename	= implode('.', $parts);
		}

		return substr($filename, 0, ($length - strlen($ext))).$ext;
	}

	public function validFilesize() {
		if ($this->max_size != 0  and  $this->file_size > $this->max_size) {
			return false;
		}
		else { 
			return true;
		}
	}

	public function getExtension($filename) {
		$x = explode('.', $filename);
		return '.'.end($x);
	}
	

	/**
	 * Process the upload message using bundle message
	 */
	public function addError($msg) {
		if(!is_array($msg)) {
			$msg = array($msg);
		}

		foreach($msg as $m) {
			$this->error_msg []= call_user_func_array('Clips\\lang', func_get_args());
		}
	}

	public function validate() {
		if ($this->upload_path == '') {
			$this->addError('The upload path is not set!');
			return false;
		}

		if (function_exists('realpath') AND @realpath($this->upload_path) !== FALSE) {
			$this->upload_path = str_replace("\\", "/", realpath($this->upload_path));
		}

		if ( ! @is_dir($this->upload_path)) {
			$this->addError('The upload path is not a directory');
			return false;
		}

		if ( ! \Clips\is_writable($this->upload_path))
		{
			$this->addError('The upload path is not writable');
			return false;
		}

		$this->upload_path = preg_replace("/(.+?)\/*$/", "\\1/",  $this->upload_path);
		return true;
	}

	public function validFileType($ignore_mime = false) {
		if ($this->allowed_types == '*') {
			return true;
		}

		if (!$this->allowed_types) {
			$this->addError('No allowed upload filetype is set!');
			return false;
		}

		$ext = strtolower(ltrim($this->file_ext, '.'));

		if (!in_array($ext, $this->allowed_types)) {
			return false;
		}

		// Images get some additional checks
		$image_types = array('gif', 'jpg', 'jpeg', 'png', 'jpe');

		if (in_array($ext, $image_types)) {
			if (getimagesize($this->file_temp) === false) {
				return false;
			}
		}

		if ($ignore_mime === false) {
			return true;
		}

		$mime = \Clips\mime_types($ext);

		if (is_array($mime)) {
			if (in_array($this->file_type, $mime, true)) {
				return true;
			}
		}
		elseif ($mime == $this->file_type) {
				return true;
		}

		return false;
	}

	public function validDimensions() {
		if ( ! $this->isImage()) {
			return true;
		}

		if (function_exists('getimagesize')) {
			$D = @getimagesize($this->file_temp);

			if ($this->max_width > 0 AND $D['0'] > $this->max_width) {
				return false;
			}

			if ($this->max_height > 0 AND $D['1'] > $this->max_height) {
				return false;
			}

			return true;
		}

		return true;
	}

	protected function _prep_filename($filename) {
		if (strpos($filename, '.') === FALSE OR $this->allowed_types == '*') {
			return $filename;
		}

		$parts		= explode('.', $filename);
		$ext		= array_pop($parts);
		$filename	= array_shift($parts);

		foreach ($parts as $part) {
			if ( ! in_array(strtolower($part), $this->allowed_types) OR $this->mimes_types(strtolower($part)) === FALSE) {
				$filename .= '.'.$part.'_';
			}
			else {
				$filename .= '.'.$part;
			}
		}

		$filename .= '.'.$ext;

		return $filename;
	}

	public function isImage() {
		$png_mimes  = array('image/x-png');
		$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');

		if (in_array($this->file_type, $png_mimes)) {
			$this->file_type = 'image/png';
		}

		if (in_array($this->file_type, $jpeg_mimes)) {
			$this->file_type = 'image/jpeg';
		}

		$img_mimes = array('image/gif', 'image/jpeg', 'image/png');

		return (in_array($this->file_type, $img_mimes, true)) ? true : false;
	}
}
