<?php namespace Clips\Controllers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Controller;

/**
 * The controller to generate the qr code using a request
 *
 * @author Jack
 * @version 1.0
 * @date Wed Mar 25 18:15:31 2015
 */
class QrController extends Controller {

	private $options = array('errorCorrection', 'moduleSize' => 'numeric',
		'size' => 'numeric', 'padding' => 'numeric', 'label', 'labelFontSize' => 'numeric');

	/**
	 * @Clips\Library({"fileCache", "qrcode"})
	 */
	public function generate($message) {
		$message = urldecode(urldecode($message)); // Decode the message first
		$id = md5($message);
		$file_name = "clips_qr_$id.png";
		if($this->filecache->exists($file_name)) {
			return $this->image($this->filecache->contents($file_name), 'png');
		}

		$qrcode = $this->qrcode->setText($message);

		$fontPath = \Clips\config('qr_label_font_path');
		if($fontPath) {
			$qrcode->setLabelFontPath($fontPath[0]);
		}

		foreach($this->options as $k => $v) {
			if(is_string($k)) {
				$name = ucfirst($k);
				$param = $this->request->param($k);
				if(!$param || !is_numeric($param)) {
					continue;
				}
			}
			else {
				$name = ucfirst($v);
				$param = $this->request->param($v);
				if(!$param) {
					continue;
				}
			}
			$method = 'set'.$name;
			call_user_func_array(array($this->qrcode, $method), array($param));
		}

		$fcolor = $this->request->param('fcolor');
		if($fcolor) {
			$qrcode->setForegroundColor(\Clips\hex2rgb($fcolor));
		}
		$bcolor = $this->request->param('bcolor');
		if($bcolor) {
			$qrcode->setBackgroundColor(\Clips\hex2rgb($bcolor));
		}

		$img = $this->qrcode->show();
		$this->filecache->save($file_name, $img);
		return $this->image($img, 'png');
	}
}
