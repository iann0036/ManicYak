<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Album extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('User');
		$this->load->model('MyMedia');
		
		$this->load->helper('url');
		if (!$this->session->userdata('username'))
			redirect('/');
	}
	
	public function index() {
		$album_data = json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=4fcb6c1c7eac0fbf013bf72ddc8ef419&autocorrect=1&artist=".urlencode($this->input->get_post('artist'))."&album=".urlencode($this->input->get_post('album'))."&format=json"),true);
		$album_data['download'] = null;
		
		$media = $this->MyMedia->search($album_data['album']['artist'],$album_data['album']['name']);
		/*if ($media==null) {
			$magnet = getMagnet($this->input->get_post('artist')." ".$this->input->get_post('album'),"Music");
			if ($magnet!=null)
				$album_data['download'] = $this->MyMedia->add('music',$magnet,$album_data['album']['artist'].' - '.$album_data['album']['name'],$album_data['album']['artist'],$album_data['album']['name']);
		} else
			$album_data['download'] = $media;*/
	
		$album_data['total_duration'] = 0;
		foreach ($album_data['album']['tracks']['track'] as $track)
			$album_data['total_duration'] += $track['duration'];
		$album_data['total_duration'] = floor($album_data['total_duration']/60);
		$album_data['album']['releasedate'] = date("F j, Y",strtotime($album_data['album']['releasedate']));
	
		$this->_header();
		$this->load->view('album',$album_data);
		$this->_footer();
	}
	
	private function _header() {
		$header_data = array(
			'title' => 'Album',
			'username' => $this->session->userdata('username'),
			'realname' => $this->session->userdata('realname')
		);
		$this->load->view('header',$header_data);
	}
	
	private function _footer() {
		$this->load->view('footer');
	}
}