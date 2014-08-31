<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subscription extends CI_Model {
	private $subscriptions;

    function __construct() {
        parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->subscriptions = array();
    }
	
	function get($all = false) {
		if (!empty($this->subscriptions))
			return $this->subscriptions;
	
		if (!$all)
			$this->db->where('username',$this->session->userdata('username'));
		$results = $this->db->get('subscription');
		foreach ($results->result() as $result) {
			$this->subscriptions[] = $result;
		}
		
		return $this->subscriptions;
	}
	
	function setInfo($id,$banner,$info) {
		$update_data = array(
			'banner' => $banner,
			'info' => $info
		);
	
		$this->db->where('id',$id);
		$this->db->update('subscription',$update_data);
	}
	
	function check($name) {
		$this->get();
		
		foreach ($this->subscriptions as $subscription) {
			if ($subscription->media_name == $name)
				return true;
		}
		
		return false;
	}

	function subscribe($name) {
		$insert_data = array(
			'username' => $this->session->userdata('username'),
			'type' => 'tv',
			'media_name' => $name
		);
		$this->db->insert('subscription',$insert_data);
	}
	
	function update($id,$season,$episode) {
		$update_data = array(
			'lastseason' => $season,
			'lastepisode' => $episode
		);
		$this->db->where('id',$id);
		$this->db->update('subscription',$update_data);
	}
	
	function unsubscribe($name) {
		$this->db->where('username',$this->session->userdata('username'));
		$this->db->where('media_name',$name);
		$this->db->delete('subscription');
	}
}