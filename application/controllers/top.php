<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Top extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		
		$this->load->helper('url');
		if (!$this->session->userdata('username'))
			redirect('/');
	}
	
	public function movie() {
		$rtdata = json_decode(file_get_contents("http://api.rottentomatoes.com/api/public/v1.0/lists/dvds/top_rentals.json?apikey=nbngvgneaxjsnuawcb5r7rh6&limit=50"),true);
		
		$topmovie_data = array(
			'movies' => $rtdata['movies']
		);
		
		for ($i=0; $i<count($topmovie_data['movies']); $i++) {
			$topmovie_data['movies'][$i]['newposter'] = "data:image/jpg;base64,".base64_encode(file_get_contents($topmovie_data['movies'][$i]['posters']['profile']));
		}
	
		$this->_header();
		$this->load->view('top_movie',$topmovie_data);
		$this->_footer();
	}
	
	public function music() {
		$lfmdata = json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=chart.gettopartists&api_key=4fcb6c1c7eac0fbf013bf72ddc8ef419&autocorrect=1&format=json"),true);
	
		$topmusic_data = array(
			'artists' => $lfmdata['artists']['artist']
		);
		
		for ($i=0; $i<count($topmusic_data['artists']); $i++) {
			$topmusic_data['artists'][$i]['poster'] = "data:image/jpg;base64,".base64_encode(file_get_contents($topmusic_data['artists'][$i]['image'][3]['#text']));
		}
	
		$this->_header();
		$this->load->view('top_music',$topmusic_data);
		$this->_footer();
	}
	
	public function tv() {
		$toptv_data = array(
			'tv' => $this->_getTV()
		);
		
		$this->_header();
		$this->load->view('top_tv',$toptv_data);
		$this->_footer();
	}
	
	private function _header() {
		$header_data = array(
			'title' => 'Popular',
			'username' => $this->session->userdata('username'),
			'realname' => $this->session->userdata('realname')
		);
		$this->load->view('header',$header_data);
	}
	
	private function _footer() {
		$this->load->view('footer');
	}
	
	private function _getTV() {
		$return = array();
	
		$doc = new DomDocument;
		@$doc->loadHTML(file_get_contents('http://thepiratebay.se/tv'));
		$dts = $doc->getElementsByTagName('dt');
		foreach ($dts as $dt) {
			$value = $dt->firstChild->nodeValue;
			if (stripos($value,'Season')===false && $value!="Show all series" && $value!="Show 50 newest uploads")
				$return[] = $dt->firstChild->nodeValue;
		}
		
		return $return;
	}
}
?>