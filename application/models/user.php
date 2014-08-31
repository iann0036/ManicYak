<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model {
    function __construct() {
        parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->library('upload');
		$this->load->library('image_lib');
    }

    function login($user,$pass) {
        $this->db->where('username',$user);
		$this->db->where('password',$this->_hash($pass));
		$users = $this->db->get('users');
		
		if ($users->num_rows() > 0) {
			$this->session->set_userdata($users->row_array());
			return true;
		}
		
		return false;
    }

	function loginFacebook($username, $realname, $email) {
		$this->db->where('username',$username);
		$users = $this->db->get('users');
		
		if ($users->num_rows() > 0) {
			$user = $users->row_array();
			if ($user['type']=='facebook') {
				$this->session->set_userdata($users->row_array());
				return true;
			}
		} else {
			$insert_data = array(
				'username' => $username,
				'type' => 'facebook',
				'realname' => $realname,
				'email' => $email
			);
			$this->db->insert('users',$insert_data);
			$this->session->set_userdata($insert_data);
			
			return true;
		}
		
		return false;
	}
	
	function updatePrefs($pref_itunes, $pref_autogather, $pref_ack) {
		$update_data = array(
			'pref_itunes' => false,
			'pref_autogather' => false,
			'pref_ack' => false
		);
		
		if ($pref_itunes=="on") {
			$update_data['pref_itunes'] = true;
			$this->session->set_userdata('pref_itunes',true);
		} else
			$this->session->set_userdata('pref_itunes',false);
		if ($pref_autogather=="on") {
			$update_data['pref_autogather'] = true;
			$this->session->set_userdata('pref_autogather',true);
		} else
			$this->session->set_userdata('pref_autogather',false);
		if ($pref_ack=="on") {
			$update_data['pref_ack'] = true;
			$this->session->set_userdata('pref_ack',true);
		} else
			$this->session->set_userdata('pref_ack',false);
		
			
		$this->db->where('username',$this->session->userdata('username'));
		$this->db->update('users',$update_data);
	}
	
	private function _doPhoto($user) {
		$config = array(
			'upload_path' => $_SERVER['DOCUMENT_ROOT'].'/images/avatars/',
			'overwrite' => true,
			'max_width' => 3840,
			'max_height' => 2160,
			'allowed_types' => 'gif|jpg|png'
		);
		$this->upload->initialize($config);
	
		if (!$this->upload->do_upload()) {
			@symlink($_SERVER['DOCUMENT_ROOT'].'/images/avatars/'.$user.'.png',$_SERVER['DOCUMENT_ROOT'].'/images/avatars/default.png');
		} else {
			unlink($_SERVER['DOCUMENT_ROOT'].'/images/avatars/'.$user.'.png');
			$original_data = $this->upload->data();
			$config = array(
				'image_library' => 'gd2',
				'source_image' => $original_data['full_path'],
				'maintain_ratio' => true,
				'width' => 28,
				'height' => 28,
				'new_image' => $_SERVER['DOCUMENT_ROOT'].'/images/avatars/'.$user.'.png'
			);
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			unlink($original_data['full_path']);
		}
	}

    function setGoogleAuth($key) {
        $update_data = array(
            'google_openid' => $key
        );

        $this->db->where('username',$this->session->userdata('username'));
        $this->db->update('users',$update_data);

        $this->session->set_userdata('google_openid',$key);
    }
	
	function update($password, $realname, $email) {
		$update_data = array(
			'realname' => $realname,
			'email' => $email
		);
		
		if ($password!=null && $password!="") {
			$update_data['password'] = $this->_hash($password);
		}
		
		$this->db->where('username',$this->session->userdata('username'));
		$this->db->update('users',$update_data);
		
		$this->session->set_userdata('realname',$realname);
		$this->session->set_userdata('email',$email);
		
		$this->_doPhoto($this->session->userdata('username'));
	}
	
	function get($user = null) {
		if ($user==null)
			$this->db->where('username',$this->session->userdata('username'));
		else
			$this->db->where('username',$user);
		$users = $this->db->get('users');
		return $users->row_array();
	}
	
	function register($user,$pass,$realname,$email) {
        $this->_notifyAdminNewUser($user,$realname,$email);

		if ($user=="default")
			return false;
	
		$this->db->where('username',$user);
		$this->db->where('password',$this->_hash($pass));
		$users = $this->db->get('users');
		
		if (!$users->num_rows() > 0) {
			$insert_data = array(
				'username' => $user,
				'password' => $this->_hash($pass),
				'realname' => $realname,
				'email' => $email
			);
			$this->db->insert('users',$insert_data);
			$this->session->set_userdata($insert_data);
			
			$this->_doPhoto($user);
			
			return true;
		} else
			error_log('Existing user registration attempt - '.$users->num_rows().' - '.var_export($user." ".$email,true));
		
		return false;
	}

    private function _notifyAdminNewUser($user,$realname,$email) {
        $to      = 'admin@manicyak.com';
        $subject = 'New Manic Yak User';
        $message = 'New Manic Yak User
------------------

Username: '.$user.'
Real Name: '.$realname.'
E-mail: '.$email.'
';
        $headers = 'From: Manic Yak <webmaster@manicyak.com>';

        mail($to, $subject, $message, $headers);
    }
	
	function logout() {
		$this->session->sess_destroy();
	}
	
	private function _hash($pass) {
		return md5($pass);
	}
}