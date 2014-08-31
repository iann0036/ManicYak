<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('MyMedia');
		$this->load->model('Subscription');
		$this->load->helper('manic');
		
		$this->load->helper('url');
		if (!$this->session->userdata('username'))
			redirect('/');
	}
	
	public function ack($id) {
		$this->MyMedia->ack($id);
		$this->output->set_output(json_encode(array(
			'status' => 'success'
		)));
	}
	
	public function unack($id) {
		$this->MyMedia->unack($id);
		$this->output->set_output(json_encode(array(
			'status' => 'success'
		)));
	}
	
	public function initiate($id) {
		$this->MyMedia->initiate($id);
		$this->output->set_output(json_encode(array(
			'status' => 'success'
		)));
	}
	
	public function delete($id) {
		$this->MyMedia->delete($id);
		$this->output->set_output(json_encode(array(
			'status' => 'success'
		)));
	}
	
	public function sidebar() {
		$media = $this->MyMedia->get();
		$new_media = array();
		
		foreach ($media as $item) {
			if ($item->status == "partial" || time() - $item->completed < 5)
				$new_media[] = $item;
		}
		
		$this->output->set_output(json_encode(array(
			'status' => 'success',
			'media' => $new_media
		)));
	}
	
	public function subscribe($name) {
		$this->Subscription->subscribe(urldecode($name));
	}
	
	public function unsubscribe($name) {
		$this->Subscription->unsubscribe(urldecode($name));
	}
	
	public function tv($series,$season,$episode) {
		$series = urldecode($series);
		$media = $this->MyMedia->get(true);
		
		foreach ($media as $item) {
			if ($item->media_name == $series && $item->media_attr == $season.";".$episode) {
				$this->output->set_output(json_encode($item));
				return;
			}
		}
		
		$magnet = getMagnet($series." S".str_pad($season,2,"0",STR_PAD_LEFT)."E".str_pad($episode,2,"0",STR_PAD_LEFT),"TV shows","HD - TV shows");
		if ($magnet!=null) {
			$id = $this->MyMedia->add("tv",$magnet,$series." Season ".$season." Episode ".$episode,$series,$season.";".$episode);
			$this->output->set_output(json_encode(array(
				'id' => $id,
				'status' => 'none'
			)));
			return;
		}
		
		$this->output->set_output(json_encode(array(
			'status' => 'unavailable'
		)));
	}
	
	public function movie($name,$hd) {
		$media = $this->MyMedia->getMediaById($this->MyMedia->search($name,(int)$hd));
		if ($media==null) {
			$magnet = getMagnet($name,"Movies");
			if ($magnet!=null) {
				$return = (object)array(
				    'id' => $this->MyMedia->add('movie',$magnet,$name,$name,$hd),
				    'status' => 'none'
				);
			} else
			    $return = (object)array(
			        'id' => null,
			        'status' => 'unavailable'
			    );
		} else
			$return = $media;
		
		$this->output->set_output(json_encode(array(
			'id' => $return->id,
			'status' => $return->status
		)));
	}
	
	public function music($artist,$album) {
		$media = $this->MyMedia->getMediaById($this->MyMedia->search(urldecode($artist),urldecode($album)));
		if ($media==null) {
			$magnet = getMagnet($artist." ".$album,"Music");
			if ($magnet!=null)
				$return = $this->MyMedia->add('music',$magnet,urldecode($artist).' - '.urldecode($album),urldecode($artist),urldecode($album));
		} else
			$return = $media;
		
		$this->output->set_output(json_encode(array(
			'id' => $return->id,
			'status' => $return->status
		)));
	}
}