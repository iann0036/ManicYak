<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('MyMedia');
		
		$this->load->helper('url');
		if (!$this->session->userdata('username'))
			redirect('/');
	}
	
	public function index() {
		$this->_header();
		$media_data = array(
			'media' => $this->MyMedia->get(true),
			'pref_itunes' => $this->session->userdata('pref_itunes')
		);
		
		for ($i=0; $i<count($media_data['media']); $i++)
			$media_data['media'][$i]->readableTime = $this->_readableDateTime($media_data['media'][$i]->added);
		
		$this->load->view('media',$media_data);
		$this->_footer();
	}
	
	private function _header() {
		$header_data = array(
			'title' => 'Media',
			'username' => $this->session->userdata('username'),
			'realname' => $this->session->userdata('realname')
		);
		$this->load->view('header',$header_data);
	}
	
	private function _footer() {
		$this->load->view('footer');
	}
	
	private function _readableDateTime($datetime) {
		$values = explode(" ", $datetime);
		$dates = explode("-", $values[0]);
		$times = explode(":", $values[1]);
		$newdate = mktime($times[0], $times[1], $times[2], $dates[1], $dates[2], $dates[0]);
		$newdate2 = $_SERVER['REQUEST_TIME']-$newdate;
		//$newdate2+=DB_TIME_OFFSET;
		if ($newdate2>172799)
			return date("F j, Y",$newdate);
		else if ($newdate2>86399)
			return "Yesterday";
		else if ($newdate2>7199)
			return intval($newdate2/3600)." hours ago";
		else if ($newdate2>3599)
			return "1 hour ago";
		else if ($newdate2>119)
			return intval($newdate2/60)." minutes ago";
		else if ($newdate2>59)
			return "1 minute ago";
		else if ($newdate2>=0)
			return "Just a moment ago";
		else
			return "In the future";
	}
}