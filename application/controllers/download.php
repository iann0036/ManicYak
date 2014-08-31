<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('MyMedia');
		
		$this->load->helper('url');
		if (!$this->session->userdata('username'))
			redirect('/');
	}

	public function index() {
		$id = $this->input->get_post('id');
		
		$media = $this->MyMedia->getMediaById($id);
		if ($media->type == "music")
			$file = $this->MyMedia->compileMusic($id);
		else if ($media->type == "movie")
			$file = $this->MyMedia->compileMovie(explode(";",$media->files));
		else if ($media->type == "tv")
			$file = $this->MyMedia->compileTv(explode(";",$media->files));
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$media->display_name.'.'.pathinfo($file,PATHINFO_EXTENSION).'"');
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.filesize($file));
		readfile($file);
	}
	
	public function playlist($id) {
		$file = $_SERVER['DOCUMENT_ROOT'].'/playlists/'.$id.'/playlist.m3u';
	
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="playlist.m3u"');
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.filesize($file));
		readfile($file);
	}
}
?>