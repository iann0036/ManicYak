<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Watch extends CI_Controller {
	public function __construct() {
		parent::__construct();
		set_time_limit(0);
		$this->load->library('session');
		$this->load->model('MyMedia');
		
		$this->load->helper('url');
		if (!$this->session->userdata('username'))
			redirect('/');
	}
	
	public function index() {
		$watch_data = array(
			'id' => $this->input->get_post('id')
		);
		$this->load->view('watch',$watch_data);
	}
	
	public function source($id) {
		$media = $this->MyMedia->getMediaById($id);
		$file = $this->_compileMedia(explode(";",$media->files));
		
		if (is_file($file)) {
			header("Content-type: video/mp4");
		 
			if (isset($_SERVER['HTTP_RANGE'])) {
				$this->_rangeDownload($file);
			} else {
				header("Content-Length: ".filesize($file));
				readfile($file);
			}
		}
	}
	
	private function _rangeDownload($file) {
		$fp = @fopen($file, 'rb');
	 
		$size   = filesize($file);
		$length = $size;
		$start  = 0;
		$end    = $size - 1;
		
		header("Accept-Ranges: 0-$length");
		if (isset($_SERVER['HTTP_RANGE'])) {
			$c_start = $start;
			$c_end   = $end;
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			if (strpos($range, ',') !== false) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				exit;
			}
			
			if ($range == '-') {
				$c_start = $size - substr($range, 1);
			}
			else {
				$range  = explode('-', $range);
				$c_start = $range[0];
				$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
			}
			$c_end = ($c_end > $end) ? $end : $c_end;
			if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				exit;
			}
			$start  = $c_start;
			$end    = $c_end;
			$length = $end - $start + 1;
			fseek($fp, $start);
			header('HTTP/1.1 206 Partial Content');
		}
		
		header("Content-Range: bytes $start-$end/$size");
		header("Content-Length: $length");
	 
		$buffer = 1024 * 8;
		while(!feof($fp) && ($p = ftell($fp)) <= $end) {
	 
			if ($p + $buffer > $end) {
				$buffer = $end - $p + 1;
			}
			set_time_limit(0);
			echo fread($fp, $buffer);
			flush();
		}
	 
		fclose($fp);
	}
	
	private function _compileMedia($files) {
		$largest;
		$largestSize = 0;
		
		foreach ($files as $file) {
			if (filesize($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file) > $largestSize) {
				$largest = $_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file;
				$largestSize = filesize($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file);
			}
		}
		
		return $largest;
	}
}
?>