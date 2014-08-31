<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {
	private $movie_data;
	private $music_data;
	private $tv_data;
	
	private $ctx;

	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->file('application/third_party/imdb.php');
		$this->load->helper('manic');
		$this->load->model('MyMedia');
		$this->load->model('Subscription');
		//$this->load->file('application/third_party/google-api-php-client/src/Google_Client.php');
		//$this->load->file('application/third_party/google-api-php-client/src/contrib/Google_YouTubeService.php');
		
		$this->load->helper('url');
		if (!$this->session->userdata('username'))
			redirect('/');
			
		$this->ctx = stream_context_create(
			array(
				'http' => array(
					'timeout' => 5
				)
			)
		);
	}
	
	public function index() {
		$this->all('search');
	}
	
	public function movie() {
		$this->all('search_movie');
	}
	
	public function music() {
		$this->all('search_music');
	}
	
	public function tv() {
		$this->all('search_tv');
	}
	
	static function _sortRelevance($a, $b) {
		return $a['rating'] < $b['rating'];
	}
	
	private function _idecode($term) {
		return str_replace("&#41;",")",str_replace("&#40;","(",urldecode($term)));
	}
	
	public function all($view = 'search_all') {
		$search_data = array(
			'status' => 'error',
			'results' => array()
		);
		
		if ($this->input->get_post('q')) {
			$this->_movie($this->_idecode($this->input->get_post('q')));
			$this->_music($this->_idecode($this->input->get_post('q')));
			$this->_tv($this->_idecode($this->input->get_post('q')));
			
			if ($view!="search_music" && $view!="search_tv") {
				$movie_data_set = $this->_processMovie(true);
				foreach ($movie_data_set as $movie_data) {
					if ($movie_data['rating']>=1) {
						$search_data['results'][] = array(
							'movie' => $movie_data['movie'],
							'type' => 'Movie',
							'shorttype' => 'movie',
							'name' => $movie_data['movie']['title'],
							'summary' => $movie_data['movie']['plot'],
							'image' => "data:image/jpg;base64,".base64_encode(file_get_contents($movie_data['movie']['poster'],false,$this->ctx)),
							'rating' => number_format($movie_data['rating'],1),
							'uniqid' => 0
						);
					}
				}
			}
			if ($view!="search_movie" && $view!="search_tv") {
				$music_data_set = $this->_processMusic(true);
				foreach ($music_data_set as $music_data) {
					if ($music_data['rating']>=1) {
						$search_data['results'][] = array(
							'music' => $music_data['music'],
							'type' => 'Music Artist',
							'shorttype' => 'music',
							'name' => $music_data['music']['artist']['name'],
							'summary' => $music_data['music']['artist']['bio']['content'],
							'image' => "data:image/jpg;base64,".base64_encode(file_get_contents($music_data['music']['artist']['image'][1]['#text'],false,$this->ctx)),
							'rating' => number_format($music_data['rating'],1),
							'uniqid' => '',
							'similar_artists' => $music_data['similar_artists'],
							'albums' => $music_data['albums']
						);
					}
				}
			}
			if ($view!="search_movie" && $view!="search_music") {
				$tv_data_set = $this->_processTv(true);
				foreach ($tv_data_set as $tv_data) {
					if ((float)$tv_data['rating']>=1) {
						$search_data['results'][] = array(
							'tv' => $tv_data['tv'],
							'type' => 'TV Show',
							'shorttype' => 'tv',
							'name' => $tv_data['tv']->Series->SeriesName,
							'summary' => $tv_data['tv']->Series->Overview,
							'image' => "data:image/jpg;base64,".base64_encode(file_get_contents('http://thetvdb.com/banners/'.$tv_data['tv']->Series->poster,false,$this->ctx)),
							'rating' => number_format((float)$tv_data['rating'],1),
							'uniqid' => $tv_data['tv']->Series->id,
							'episodes' => $this->_organiseEpisodes($tv_data['tv']),
							'subscribed' => $this->Subscription->check($tv_data['tv']->Series->SeriesName)
						);
					}
				}
			}
			
			usort($search_data['results'],array('Search','_sortRelevance'));
			$search_data['original_term'] = ucwords($this->_idecode($this->input->get_post('q')));
		}
		
		$this->_header();
		if ($view=="search" && $this->input->get_post('q') && count($search_data['results'])>0)
			$view = "search_".$search_data['results'][0]['shorttype'];
		if ($view=="search_all")
			$this->load->view($view,$search_data);
		else if ($view!="search") {
			$index_offset = 0;
			if ($this->input->get_post('uniqid')) { // if uniqid given
				for ($i=0; $i<count($search_data['results']); $i++) {
					if ($this->input->get_post('uniqid') == $search_data['results'][$i]['uniqid']) {
						$index_offset = $i;
						break;
					}
				}
			}
			$search_data['results'][$index_offset]['original_term'] = ucwords($this->_idecode($this->input->get_post('q')));
			$this->load->view($view,$search_data['results'][$index_offset]);
		} else {
			$this->load->view('search');
		}
			
		$this->_footer();
	}
	
	private function _organiseEpisodes($episodes) {
		$episodes = json_decode(json_encode($episodes),true); // this is the devil (SimpleXMLElement)
		$episodes = $episodes['Episode'];
	
		$episode_array = array();
		foreach ($episodes as $episode) {
			if ($episode['Combined_episodenumber'] > 0 && $episode['Combined_season'] > 0) {
				if (!isset($episode_array[(int)$episode['Combined_season']]))
					$episode_array[(int)$episode['Combined_season']] = array();
				$episode_array[(int)$episode['Combined_season']][] = $episode;
			}
		}
		
		return $episode_array;
	}
	
	private function _movie($movie) {
		$imdb = new Imdb();
		$this->movie_data = array($imdb->getMovieInfo($movie));
	}
	
	private function _music($music) {
		$this->music_data = array(json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&api_key=4fcb6c1c7eac0fbf013bf72ddc8ef419&autocorrect=1&artist=".urlencode($music)."&format=json",false,$this->ctx),true));
	}
	
	private function _tv($tv) {
		$this->tv_data = array();
	
		$context  = stream_context_create(array('http' => array('timeout' => 5, 'header' => 'Accept: application/xml')));
		$url = "http://thetvdb.com/api/GetSeries.php?seriesname=".urlencode($tv);

		$xml = file_get_contents($url, false, $context);
		$xml = simplexml_load_string($xml);
		$series_set = $xml->Series;
		if (count($series_set)==0)
			return;
		foreach ($series_set as $series) {
			file_put_contents('/tmp/'.$series->seriesid.'_overview.zip',file_get_contents('http://thetvdb.com/api/3B63B448BFDD1CBB/series/'.$series->seriesid.'/all/en.zip',false,$this->ctx));
			$zip = new ZipArchive(); 
			$zip->open('/tmp/'.$series->seriesid.'_overview.zip'); 
			$zip->extractTo('/tmp/'.$series->seriesid.'_overview/'); 
			$zip->close();
			$this->tv_data[] = simplexml_load_file('/tmp/'.$series->seriesid.'_overview/en.xml');
		}
	}
	
	private function _header() {
		$header_data = array(
			'title' => 'Search',
			'username' => $this->session->userdata('username'),
			'realname' => $this->session->userdata('realname')
		);
		$this->load->view('header',$header_data);
	}
	
	private function _footer() {
		$this->load->view('footer');
	}
	
	private function _processMovie($aggregate = false) {
		$return = array();
		foreach ($this->movie_data as $movie_data) {
			if (!isset($movie_data['type'])) // non existant
				continue;
			if ($movie_data['type'] != "movie") // tv show
				continue;
			$return_item = array(
				'status' => 'success',
				'movie' => $movie_data,
				'rating' => $movie_data['rating']
			);
			
			$return_item['movie']['title'] = str_replace("&#x22;","",$return_item['movie']['title']);
			
			$return_item['movie']['trailer'] = $this->_getTrailer($return_item['movie']['title']);
			
			$return[] = $return_item;
		}
		
		if ($aggregate)
			return $return;
		return $return[0];
	}
	
	private function _getTrailer($term) {
		$DEVELOPER_KEY = 'AIzaSyBPS6LHmUO6stTdvjBZeHeYsqej1tTECWU';
		
		$client = new Google_Client();
		$client->setDeveloperKey($DEVELOPER_KEY);
		$youtube = new Google_Service_YouTube($client);

		try {
			$searchResponse = $youtube->search->listSearch('id,snippet', array(
				'q' => $term." trailer",
				'maxResults' => 1
			));

			$videos = '';

			foreach ($searchResponse['items'] as $searchResult) {
				return $searchResult['id']['videoId'];
			}
		} catch (Google_ServiceException $e) {
			$htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage()));
		} catch (Google_Exception $e) {
			$htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage()));
		}
	}
	
	private function _processMusic($aggregate = false) {
		$return = array();
		foreach ($this->music_data as $music_data) {
			if (!isset($music_data['artist']['stats']['listeners'])) // no results
				continue;
			$return_item = array(
				'status' => 'success',
				'music' => $music_data,
				'similar_artists' => '',
				'rating' => log(sqrt($music_data['artist']['stats']['listeners']+1),3)
			);
			
			$return_item['albums'] = json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.gettopalbums&api_key=4fcb6c1c7eac0fbf013bf72ddc8ef419&autocorrect=1&artist=".urlencode($return_item['music']['artist']['name'])."&format=json",false,$this->ctx),true);
			
			foreach ($return_item['music']['artist']['similar']['artist'] as $artist) {
				$return_item['similar_artists'].='<a href="#">'.$artist['name'].'</a>, ';
			}
			$return_item['similar_artists'] = substr($return_item['similar_artists'],0,-2);
			$return[] = $return_item;
		}
		
		if ($aggregate)
			return $return;
		return $return[0];
	}
	
	private function _processTv($aggregate = false, $id = false) {
		$return = array();
		foreach ($this->tv_data as $tv_data) {
			$return[] = array(
				'status' => 'success',
				'tv' => $tv_data,
				'rating' => (float)$tv_data->Series->Rating*min(log((int)$tv_data->Series->RatingCount+1,2),10)/10
			);
		}
		
		if ($aggregate)
			return $return;
		return $return[0];
	}
}