<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

set_include_path($_SERVER['DOCUMENT_ROOT']."/application/third_party/google-api-php-client-master/src/" . PATH_SEPARATOR . get_include_path());
require_once 'Google/Client.php';
require_once 'Google/Http/MediaFileUpload.php';
require_once 'Google/Service/Drive.php';

class Settings extends CI_Controller {
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
		if ($this->input->get_post('formpost'))
			$this->User->updatePrefs($this->input->get_post('pref_itunes'),$this->input->get_post('pref_autogather'),$this->input->get_post('pref_ack'));
		$settings_data = $this->User->get();
	
		$this->_header();
		$this->load->view('settings',$settings_data);
		$this->_footer();
	}

    public function drive() {
        if ($this->session->userdata('google_openid')) {
            $new_token = $this->MyMedia->driveRefreshToken($this->session->userdata('google_openid'));
        } else if ($this->input->get('code')) {
            $new_token = $this->MyMedia->driveRefreshToken($this->input->get('code'));
        } else {
            $this->MyMedia->driveAuthenticate();
        }

        $this->User->setGoogleAuth($new_token);

        $this->_driveSetup();

        redirect('/settings/');
    }

    public function drivetest() {
        $this->MyMedia->addToDrive(11);
    }

    private function _driveSetup() {
        $manicRootId = null;
        $client_id = '660857930175-8elhqa0qmss6hmf45d72su2o9mv4jqhp.apps.googleusercontent.com';
        $client_secret = 'g_DGtr9ZL4fwoeEI92BwDQWt';
        $redirect_uri = 'http://manicyak.com/settings/drive/';

        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->setScopes(array('https://www.googleapis.com/auth/drive'));
        $client->setAccessToken($this->session->userdata('google_openid'));

        $service = new Google_Service_Drive($client);

        $search = $service->files->listFiles(array('q' => 'title = "Manic Yak" AND trashed = false AND mimeType = "application/vnd.google-apps.folder" AND "root" in parents'));
        $items = $search->getItems();
        $manicRootId = $items[0]->id;

        if ($manicRootId==null) {
            $folder = new Google_Service_Drive_DriveFile();
            $folder->setTitle("Manic Yak");
            $folder->setDescription("Movies, Music and TV Show downloads from http://manicyak.com/");
            $folder->setMimeType("application/vnd.google-apps.folder");
            $mainFolder = $service->files->insert($folder);

            $folder = new Google_Service_Drive_DriveFile();
            $folder->setTitle("Movies");
            $folder->setDescription("Movie downloads from http://manicyak.com/");
            $folder->setMimeType("application/vnd.google-apps.folder");
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($mainFolder->getId());
            $folder->setParents(array($parent));
            $moviesFolder = $service->files->insert($folder);

            $folder = new Google_Service_Drive_DriveFile();
            $folder->setTitle("Music");
            $folder->setDescription("Music downloads from http://manicyak.com/");
            $folder->setMimeType("application/vnd.google-apps.folder");
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($mainFolder->getId());
            $folder->setParents(array($parent));
            $musicFolder = $service->files->insert($folder);

            $folder = new Google_Service_Drive_DriveFile();
            $folder->setTitle("TV Shows");
            $folder->setDescription("TV Show downloads from http://manicyak.com/");
            $folder->setMimeType("application/vnd.google-apps.folder");
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($mainFolder->getId());
            $folder->setParents(array($parent));
            $tvFolder = $service->files->insert($folder);
        }
    }
	
	private function _header() {
		$header_data = array(
			'title' => 'Settings',
			'username' => $this->session->userdata('username'),
			'realname' => $this->session->userdata('realname')
		);
		$this->load->view('header',$header_data);
	}
	
	private function _footer() {
		$this->load->view('footer');
	}
}
?>