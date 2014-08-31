<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->file('application/third_party/facebook-php-sdk/src/facebook.php');
	}
	
	public function index() {
		$config = array(
			'appId' => '556889977720561',
			'secret' => '7ed63716dfd485c3edfc425b4ac8045a',
			'fileUpload' => true,
			'allowSignedRequest' => false,
		);
		$facebook = new Facebook($config);
		$user = $facebook->getUser();
		
		if ($user) {
			$params = array('next' => 'http://manicyak.com/logout/');

			header("Location: ".$facebook->getLogoutUrl($params));
		}
		$this->session->sess_destroy();
		redirect('/');
	}
}