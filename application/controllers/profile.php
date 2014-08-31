<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller {
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
		$alert = null;
	
		if ($this->input->get_post('realname') && $this->session->userdata('type')=='internal') {
			$this->User->update($this->input->get_post('password'),$this->input->get_post('realname'),$this->input->get_post('email'));
			
			$alert = array(
				'type' => 'success',
				'message' => '<strong>SUCCESS: </strong>Your profile has been updated'
			);
		}
	
		$profile_data = array(
			'user' => $this->User->get(),
			'downloads' => $this->_getDownloadText()
		);
	
		$this->_header($alert);
		$this->load->view('profile',$profile_data);
		$this->_footer();
	}
	
	private function _getDownloadText() {
		$downloads = ((int)$this->MyMedia->getTotalDownloads()/1024/1024);
		
		return $downloads;
	}
	
	private function _header($alert = null) {
		$header_data = array(
			'title' => 'Profile',
			'username' => $this->session->userdata('username'),
			'realname' => $this->session->userdata('realname')
		);
		if ($alert!=null)
			$header_data['alert'] = $alert;
		$this->load->view('header',$header_data);
	}
	
	private function _footer() {
		$this->load->view('footer');
	}
}
?>