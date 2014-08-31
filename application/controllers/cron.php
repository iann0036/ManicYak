<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('Subscription');
		$this->load->model('User');
		$this->load->helper('manic');
	}
	
	private function _tv($series,$season,$episode) {
		$series = urldecode($series);
		
		$magnet = getMagnet($series." S".str_pad($season,2,"0",STR_PAD_LEFT)."E".str_pad($episode,2,"0",STR_PAD_LEFT),"TV shows","HD - TV shows");
		if ($magnet!=null) {
			//$id = $this->MyMedia->add("tv",$magnet,$series." Season ".$season." Episode ".$episode,$series,$season.";".$episode);
			return true;
		}
		
		return false;
	}
	
	public function index() {
	        ;
	}
	
	public function index2() {
		$subscriptions = $this->Subscription->get(true); // get all

		foreach ($subscriptions as $subscription) {
			echo $subscription->username.": ".$subscription->media_name." - ".$subscription->lastseason." / ".$subscription->lastepisode."<br />";
			if ($subscription->lastseason==null)
				$data = $this->_getLastNew($subscription->media_name,$subscription->id);
			else
				$data = $this->_getLastExisting($subscription->media_name,$subscription->lastseason,$subscription->lastepisode);
			
			if ($data['season'] > 0) {
				$this->Subscription->update($subscription->id,$data['season'],$data['episode']);
				if ($subscription->lastseason!=null && ($data['season']!=$subscription->lastseason || $data['episode']!=$subscription->lastepisode))
					$this->_notify($subscription,$data);
			}
		}
	}
	
	private function _notify($subscription,$data) {
		$user = $this->User->get($subscription->username);
		$email = $user['email'];
		$this->_mail($subscription->media_name,$data['season'],$data['episode'],$user['realname'],$user['email'],$user['username'],$subscription->banner,$subscription->info); // TBD
	}
	
	private function _mail($name,$season,$episode,$realname,$email,$username,$banner,$info) {
        // subject
        $subject = 'New '.$name.' episode';
        
        // message
        $message = '<html>
        <head></head>
        <body style="margin: 0; padding: 0;">
        <div style="background-color: #EEE; width: 640px;">
		<center>
        <img alt="Banner" src="http://thetvdb.com/banners/'.$banner.'" style="margin-bottom: 6px; width: auto; height: auto; max-width: 640px;" /><br />
        <img style="margin: 6px;" src="http://manicyak.com/images/yak_75.png" />
        <h3 style="margin-left: 8px; margin-right: 8px; color: #555; font-size: 13pt; font-weight: bold; font-family: arial,sans-serif;">A new '.$name.' episode has been released!</h3>
        <p><a target="_blank" href="http://manicyak.com/search/tv/?q='.rawurlencode($name).'"><button style="font-weight: bold; padding: 10px 15px; background: #4479BA; color: #FFF;">Watch Now</button></a></p>
        <p style="color: #555; font-size: 10pt; font-family: arial,sans-serif;"><b>Season: </b>'.$season.'</p>
        <p style="color: #555; font-size: 10pt; font-family: arial,sans-serif;"><b>Episode: </b>'.$episode.'</p>
        <p style="margin-left: 8px; margin-right: 8px; color: #555; font-size: 10pt; font-family: arial,sans-serif; margin-right: 8px;"><b>Info: </b>'.$info.'</p>
        <br /><hr />
        <p style="margin-left: 8px; margin-right: 8px; color: #555; font-size: 8pt; font-family: arial,sans-serif;">This email was sent to '.$email.'. To ensure that you continue receiving our emails, please add us to your address book or safe list.</p>
        <p style="margin-bottom: 0px; font-size: 8pt; font-weight: bold; font-family: arial,sans-serif;"><a target="_blank" href="http://manicyak.com/settings/">Unsubscribe</a></p>&nbsp;</center>
        </div>
        </body>
        </html>
        ';
        
        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        
        // Additional headers
        $headers .= 'To: '.$realname.' <'.$email.'>' . "\r\n";
        $headers .= 'From: Manic Yak <noreply@manicyak.com>' . "\r\n";
		$headers .= 'Bcc: Manic Admin <admin@manicyak.com>' . "\r\n";
		
        // Mail it
        mail(null, $subject, $message, $headers);
    }
	
	private function _getLastNew($name,$subscription_id) {
		$tctx = stream_context_create(
			array(
				'http' => array(
					'timeout' => 5
				)
			)
		);
		$context  = stream_context_create(array('http' => array('timeout' => 5, 'header' => 'Accept: application/xml')));
		$url = "http://thetvdb.com/api/GetSeries.php?seriesname=".str_replace("%26%2341%3B",")",str_replace("%26%2340%3B","(",urlencode($name)));

		$xml = file_get_contents($url, false, $context);
		$xml = simplexml_load_string($xml);
		$series_set = $xml->Series;
		$series = $series_set[0];
		
		file_put_contents('/tmp/'.$series->seriesid.'_overview.zip',file_get_contents('http://thetvdb.com/api/3B63B448BFDD1CBB/series/'.$series->seriesid.'/all/en.zip',false,$tctx));
		$zip = new ZipArchive(); 
		$zip->open('/tmp/'.$series->seriesid.'_overview.zip'); 
		$zip->extractTo('/tmp/'.$series->seriesid.'_overview/'); 
		$zip->close();
		$tv_data = simplexml_load_file('/tmp/'.$series->seriesid.'_overview/en.xml');
		$tv_data = json_decode(json_encode($tv_data),true);
		$lastep = array_pop($tv_data['Episode']);
		
		/***************************/
		
		$lastseason = (int)$lastep['Combined_season'];
		$lastepisode = (int)$lastep['Combined_episodenumber'];
		while (!$this->_tv($name,$lastseason,$lastepisode)) {
			echo ".";
			$lastepisode--;
			if ($lastepisode<1) {
				if (empty($tv_data['Episode'])) { // edge case, no eps available
					return array(
						'season' => 0,
						'episode' => 0
					);
				}
				$lastep = array_pop($tv_data['Episode']);
				$lastseason = (int)$lastep['Combined_season'];
				$lastepisode = (int)$lastep['Combined_episodenumber'];
			}
		}
		
		$this->Subscription->setInfo($subscription_id,$tv_data['Series']['banner'],$tv_data['Series']['Overview']);
		
		return array(
			'season' => $lastseason,
			'episode' => $lastepisode
		);
	}
	
	private function _getLastExisting($name,$lastseason,$lastepisode) {
		while ($this->_tv($name,$lastseason,1))
			$lastseason++;
		$lastseason--;
		
		while ($this->_tv($name,$lastseason,$lastepisode))
			$lastepisode++;
		$lastepisode--;
	
		return array(
			'season' => $lastseason,
			'episode' => $lastepisode
		);
	}
}
?>