<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

set_include_path($_SERVER['DOCUMENT_ROOT']."/application/third_party/google-api-php-client-master/src/" . PATH_SEPARATOR . get_include_path());
require_once 'Google/Client.php';
require_once 'Google/Http/MediaFileUpload.php';
require_once 'Google/Service/Drive.php';
require_once 'Google/Service/YouTube.php';


class MyMedia extends CI_Model {
	private $media = null;

    function __construct() {
        parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->helper('manic');
        $this->load->model('User');
    }
	
	function unack($id) {
		$update_data = array(
			'ack' => false
		);
		$this->db->where('id',$id);
		$this->db->update('media',$update_data);
	}
	
	function ack($id) {
		$update_data = array(
			'ack' => true
		);
		$this->db->where('id',$id);
		$this->db->update('media',$update_data);
	}

    function get($actionable = false) {
		if ($this->media!=null)
			return $this->media;
		
		if ($actionable) {
			$where = "username='".mysql_real_escape_string($this->session->userdata('username'))."' AND (status='complete' OR status='partial')";
			$this->db->where($where,null,false);
		} else
			$this->db->where('username',$this->session->userdata('username'));
		$results = $this->db->get('media');
		
		$this->media = array();
		foreach ($results->result() as $result) {
			if ($result->status=="partial") {
				if ($result->torr_id==null)
					$result->percentile = -1;
				else
					$result->percentile = getPercentile($result->torr_id);
			}
			$this->media[] = $result;
		}
		
		foreach ($this->media as $item) { // check after the fact for complete
			if ($item->status=="partial" && $item->percentile==100) {
				$this->complete($item->id,getFiles($item->torr_id),getSize($item->torr_id));
				removeTor($item->torr_id);
			}
		}
		
		return $this->media;
	}
	
	function getTotalDownloads() {
		$total = 0;
	
		$this->get();
		foreach ($this->media as $item)
			$total+=$item->size;
		
		return $total;
	}
	
	function getMediaById($id) {
		$this->get();
		foreach ($this->media as $item) {
			if ($item->id == $id)
				return $item;
		}
		
		return null;
	}
	
	function search($media_name, $media_attr) {
		$this->get();
		foreach ($this->media as $item) {
			if ($item->media_name == urldecode($media_name) && $item->media_attr == urldecode($media_attr))
				return $item->id;
		}
		
		return null;
	}
	
	function delete($id) {
		$this->get();
		foreach ($this->media as $item) {
			if ($item->id == $id)
				$this->_deleteFiles($item->files);
		}
		
		$this->db->where('id',$id);
		$this->db->delete('media');
	}
	
	function _deleteFiles($files_str) {
		$files = explode(";",$files_str);
		foreach ($files as $file)
			unlink($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file);
	}
	
	function initiate($id) {
		$this->get();
		
		foreach ($this->media as $item) {
			if ($item->id == $id) {
				$magnet = $item->magnet;
				break;
			}
		}
	
		$update_data = array(
			'status' => 'partial',
			'torr_id' => addMagnet($magnet)
		);
		
		$this->db->where('id',$id);
		$this->db->update('media',$update_data);
	}
	
	function _streamable($files) {
		$largest = null;
		$largestSize = 0;
		
		$files = explode(";",$files);
		foreach ($files as $file) {
			if (filesize($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file) > $largestSize) {
				$largest = $_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file;
				$largestSize = filesize($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file);
			}
		}
		
		return (pathinfo($largest,PATHINFO_EXTENSION)=="mp4");
	}
	
	function _createPlaylist($id, $files) {
		$files = explode(";",$files);
		$item = $this->getMediaById($id);
		if ($item->type!="music")
			return;
		
		$album_data = json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=SNIPPED&autocorrect=1&artist=".urlencode($item->media_name)."&album=".urlencode($item->media_attr)."&format=json"),true);

		$directory = $_SERVER['DOCUMENT_ROOT']."/playlists/".$id;
		mkdir($directory);
		
		$ext = "";
		
		foreach ($album_data['album']['tracks']['track'] as $track) {
			foreach ($files as $file) {
				if (stripos(preg_replace("/[^a-zA-Z0-9]+/","",$file),preg_replace("/[^a-zA-Z0-9]+/","",$track['name']))!==false) { // pure magic
					copy($_SERVER['DOCUMENT_ROOT']."/downloads/".$file,$directory."/".$track['name'].".mp3");
					$ext.="http://".$_SERVER['SERVER_NAME']."/playlists/".$id."/".rawurlencode($track['name']).".mp3
";
					continue;
				}
			}
		}
		
		file_put_contents($directory."/playlist.m3u",$ext);
	}
	
	function complete($id,$files,$size) {
		$update_data = array(
			'status' => 'complete',
			'completed' => time(),
			'files' => $files,
			'size' => $size,
			'streamable' => $this->_streamable($files)
		);
		
		$this->_createPlaylist($id, $files);
        $this->addToDrive($id);
		
		$this->db->where('id',$id);
		$this->db->update('media',$update_data);
	}

    function addToDrive($id) {
        if ($this->session->userdata('google_openid')) {
            $media = $this->MyMedia->getMediaById($id);
            if ($media->type == "music") {
                $fileuri = $this->MyMedia->compileMusic($id);
                $folderRootName = "Music";
            } else if ($media->type == "movie") {
                $fileuri = $this->MyMedia->compileMovie(explode(";",$media->files));
                $folderRootName = "Movies";
            } else if ($media->type == "tv") {
                $fileuri = $this->MyMedia->compileTv(explode(";",$media->files));
                $folderRootName = "TV Shows";
            }

            $filename = $media->display_name.'.'.pathinfo($fileuri,PATHINFO_EXTENSION);

            $folderRootId = null;
            $client_id = 'SNIPPED';
            $client_secret = 'SNIPPED';
            $redirect_uri = 'http://manicyak.com/settings/drive/';

            try {
                $client = new Google_Client();
                $client->setClientId($client_id);
                $client->setClientSecret($client_secret);
                $client->setRedirectUri($redirect_uri);
                $client->setScopes(array('https://www.googleapis.com/auth/drive'));
                $client->setAccessToken($this->session->userdata('google_openid'));

                $service = new Google_Service_Drive($client);

                $search = $service->files->listFiles(array('q' => 'title = "'.$folderRootName.'" AND trashed = false AND mimeType = "application/vnd.google-apps.folder"'));
                $items = $search->getItems();
                $folderRootId = $items[0]->id;

                $file = new Google_Service_Drive_DriveFile();
                $file->title = $filename;
                $file->setDescription("Download from http://manicyak.com/");
                $parent = new Google_Service_Drive_ParentReference();
                $parent->setId($folderRootId);
                $file->setParents(array($parent));
                $chunkSizeBytes = 5*1024*1024;
                $numChunks = ceil(filesize($fileuri)/$chunkSizeBytes);

                $client->setDefer(true);
                $request = $service->files->insert($file);

                $media = new Google_Http_MediaFileUpload(
                    $client,
                    $request,
                    null,
                    null,
                    true,
                    $chunkSizeBytes
                );
                $media->setFileSize(filesize($fileuri));

                $status = false;
                $handle = fopen($fileuri, "rb");
                $count = 0;
                while (!$status && !feof($handle)) {
                    $chunk = fread($handle, $chunkSizeBytes);
                    $status = $media->nextChunk($chunk);
                    $count++;
                    error_log("Processed ".$count." from ".$numChunks);
                }

                $result = false;
                if ($status != false) {
                    $result = $status;
                }

                fclose($handle);
            } catch (Google_Auth_Exception $e) {
                //die($e->getCode()." - ".$e->getMessage());
                $this->driveRefreshToken($this->session->userdata('google_openid'));
            }
            echo "<hr>Done!";
        }
    }
	
	function add($type, $magnet, $display_name, $media_name, $media_attr) {
		$insert_data = array(
			'type' => $type,
			'username' => $this->session->userdata('username'),
			'status' => 'none',
			'magnet' => $magnet,
			'torr_id' => null,
			'display_name' => urldecode($display_name),
			'media_name' => urldecode($media_name),
			'media_attr' => urldecode($media_attr)
		);
		error_log(var_export($insert_data,true));
		$this->db->insert('media',$insert_data);
		
		return $this->db->insert_id();
	}

    public function compileTv($files) {
        $largest = null;
        $largestSize = 0;

        foreach ($files as $file) {
            if (filesize($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file) > $largestSize) {
                $largest = $_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file;
                $largestSize = filesize($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file);
            }
        }

        return $largest;
    }

    public function compileMusic($id) {
        $zip = new ZipArchive();
        $filename = "/tmp/".uniqid().".zip";
        $zip->open($filename,ZipArchive::CREATE);

        if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/playlists/'.$id)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry!="." && $entry!="..")
                    $zip->addFile($_SERVER['DOCUMENT_ROOT'].'/playlists/'.$id.'/'.$entry,$entry);
            }
        } else
            error_log('Cannot process album directory handle');

        $zip->close();
        error_log($zip->status);
        error_log($filename);
        error_log(filesize($filename));
        return $filename;
    }

    public function compileMovie($files) {
        $largest = null;
        $largestSize = 0;

        foreach ($files as $file) {
            if (filesize($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file) > $largestSize) {
                $largest = $_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file;
                $largestSize = filesize($_SERVER['DOCUMENT_ROOT'].'/downloads/'.$file);
            }
        }

        return $largest;
    }

    public function driveRefreshToken($old_token) {
        $client_id = 'SNIPPED';
        $client_secret = 'SNIPPED';
        $redirect_uri = 'http://manicyak.com/settings/drive/';

        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->setAccessType('offline');
        $client->addScope("https://www.googleapis.com/auth/drive");

        $new_token = $client->authenticate($old_token);
        $this->User->setGoogleAuth($new_token);
        return $new_token;
    }

    public function driveAuthenticate() {
        $client_id = 'SNIPPED';
        $client_secret = 'SNIPPED';
        $redirect_uri = 'http://manicyak.com/settings/drive/';

        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->setAccessType('offline');
        $client->addScope("https://www.googleapis.com/auth/drive");
        $client->setApprovalPrompt('force');

        header("Location: ".$client->createAuthUrl());
    }
}