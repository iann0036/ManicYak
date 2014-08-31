<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('User');
		$this->load->model('Subscription');
	}
	
	public function index() {
		if (!$this->session->userdata('username')) {
			if ($this->input->get_post('username') && $this->input->get_post('password')) {
				if ($this->User->login($this->input->get_post('username'),$this->input->get_post('password')))
					$this->_dashboard();
				else
					$this->_login(true);
			} else
				$this->_login();
		} else
			$this->_dashboard();
	}
	
	private function _login($error = false) {
		$login_data = array();
		if ($error)
			$login_data['alert'] = array(
				'type' => 'danger',
				'message' => '<strong>ERROR: </strong>Invalid credentials'
			);
		$this->load->view('login',$login_data);
	}
	
	private function _dashboard() {
		$dashboard_data = array(
			'subscriptions' => $this->Subscription->get()
		);
		
		$this->_header();
		$this->load->view('dashboard',$dashboard_data);
		$this->_footer();
	}
	
	private function _header() {
		$header_data = array(
			'title' => 'Dashboard',
			'username' => $this->session->userdata('username'),
			'realname' => $this->session->userdata('realname')
		);
		$this->load->view('header',$header_data);
	}
	
	private function _footer() {
		$this->load->view('footer');
	}
}