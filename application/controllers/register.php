<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->helper('url');
		$this->load->library('session');
		$this->session->sess_destroy();
	}
	
	public function index() {
		if ($this->input->get_post('username')) {
			if ($this->_register($this->input->get_post('username'),$this->input->get_post('password'),$this->input->get_post('realname'),$this->input->get_post('email'))) {
				redirect('/');
			} else {
				$register_data = array();
				$register_data['alert'] = array(
					'type' => 'danger',
					'message' => '<strong>ERROR: </strong>Registration failed'
				);
				$this->load->view('register',$register_data);
			}
		} else
			$this->load->view('register');
	}
	
	private function _register($user,$pass,$realname,$email) {
		return $this->User->register($user,$pass,$realname,$email);
	}
}
