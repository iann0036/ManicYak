<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fb extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('User');
		$this->load->helper('url');
		$this->load->file('application/third_party/facebook-php-sdk/src/facebook.php');
	}
	
	public function index() {
		;
	}
	
	public function generate($id) {
		$data = json_decode(file_get_contents("https://graph.facebook.com/".$id),true);
		
		if ($data['category']=="Tv show")
			echo '<html>
		<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video#">
			<meta property="fb:app_id" content="556889977720561" />
			<meta property="og:site_name" content="Manic Yak" />
			<meta property="og:title" content="'.$data['name'].'" />
			<meta property="og:image" content="'.$data['cover']['source'].'" />
			<meta property="og:url" content="'.$data['link'].'" />
			<meta property="og:type" content="video.movie" />
		</head>
		</html>
		';
		else
			echo '<html>
		<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# music: http://ogp.me/ns/music#">
			<meta property="fb:app_id" content="556889977720561" />
			<meta property="og:site_name" content="Manic Yak" />
			<meta property="og:title" content="Owl City - The Midsummer Station" />
			<meta property="og:image" content="https://fbexternal-a.akamaihd.net/safe_image.php?d=AQDzXMOwp_BeFZlc&w=300&h=300&url=http%3A%2F%2Fupload.wikimedia.org%2Fwikipedia%2Fen%2Fb%2Fbf%2FOwl_City_-_The_Midsummer_Station_cover_art.jpg" />
			<meta property="og:alter_url" content="http://manicyak.com/album/?artist=Owl%20City&album=The%20Midsummer%20Station" />
			<meta property="og:type" content="music.album" />
			<meta http-equiv="refresh" content="0; url=http://manicyak.com/album/?artist=Owl%20City&album=The%20Midsummer%20Station">
		</head>
		</html>
		';
	}
	
	public function post($term,$action) {
		$config = array(
			'appId' => '556889977720561',
			'secret' => '7ed63716dfd485c3edfc425b4ac8045a',
			'fileUpload' => true,
			'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
		);
		
		if ($action=="music.listens")
			$type = "album";

		$facebook = new Facebook($config);
		$user = $facebook->getUser();
		
		if ($user) {
			try {
				$response = $facebook->api(
					'search',
					'GET',
					array(
						'q' => urldecode($term),
						'type' => 'page'
					)
				);
				if (count($response['data'])>0) {
					var_dump($response['data']);
					$id = $response['data'][0]['id'];
					var_dump($id);
					$response = $facebook->api(
						'me/'.$action,
						'POST',
						array(
							$type => "http://manicyak.com/fb/generate/".$id
						)
					);
					var_dump($response);
				} else {
					echo "No results found.";
				}
			} catch (FacebookApiException $e) {
				echo "Error: ".$e;
				$user = null;
				echo "<br><a href='".$facebook->getLoginUrl(array('scope' => 'user_about_me, publish_actions, email'))."'>Login</a>";
			}
		} else {
			header("Location: ".$facebook->getLoginUrl(array('scope' => 'user_about_me, publish_actions, email,manage_notifications')));
		}
	}
	
	public function deleteall($username) {
		$config = array(
			'appId' => '556889977720561',
			'secret' => '7ed63716dfd485c3edfc425b4ac8045a',
			'fileUpload' => true,
			'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
		);
		$facebook = new Facebook($config);
		
		while (true) {
			$request = $facebook->api("/".$username."/apprequests");
			var_dump($request); die();

			foreach ($request['data'] as $data) {
				try {
					$request_id = $data['id'];
					$delete_success = $facebook->api("/".$request_id,'DELETE');
					if ($delete_success) {
						echo "Successfully deleted " . $request_id;
					} else {
						echo "Delete failed".$request_id;
					}
				} catch (FacebookApiException $e) {
					echo $e;
				}
			}
		}
	}
	
	public function notify($username,$message) {
		$config = array(
			'appId' => '556889977720561',
			'secret' => '7ed63716dfd485c3edfc425b4ac8045a',
			'fileUpload' => true,
			'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
		);
		$facebook = new Facebook($config);
		
		$APPLICATION_ID = "556889977720561";
		$APPLICATION_SECRET = "7ed63716dfd485c3edfc425b4ac8045a";

		$token_url =    "https://graph.facebook.com/oauth/access_token?" .
						"client_id=" . $APPLICATION_ID .
						"&client_secret=" . $APPLICATION_SECRET .
						"&grant_type=client_credentials";
		$app_token = file_get_contents($token_url);
		$app_token = str_replace("access_token=","",$app_token);

		$data = array(
			'href'=> 'https://manicyak.com/',
			'access_token'=> $app_token,
			'template'=> urldecode($message)
		);
		$sendnotification = $facebook->api('/'.$username.'/notifications','post',$data);
		var_dump($sendnotification);
	}
	
	public function login() {
		$config = array(
			'appId' => '556889977720561',
			'secret' => '7ed63716dfd485c3edfc425b4ac8045a',
			'fileUpload' => true,
			'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
		);
		$facebook = new Facebook($config);
		$user = $facebook->getUser();
		
		if ($user) {
			try {
				$user_profile = $facebook->api('/me');
				if ($this->User->loginFacebook($user_profile['id'],$user_profile['name'],$user_profile['email']))
					redirect('/');
				else
					die('Manic Yak - Error 17');
			} catch (FacebookApiException $e) {
				error_log($e);
				header("Location: ".$facebook->getLoginUrl());
			}
		} else {
			header("Location: ".$facebook->getLoginUrl());
		}
	}
}