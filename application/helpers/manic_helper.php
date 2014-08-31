<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/application/third_party/TransmissionRPC.class.php');
	error_log(E_ALL);
	function orderBySeeds($a, $b) { return $a->seeders < $b->seeders; }
	
	function getMagnet($term, $category = null, $category2 = null) {
		$tctx = stream_context_create(
			array(
				'http' => array(
					'timeout' => 5
				)
			)
		);
		$results = json_decode(@file_get_contents("http://apify.ifc0nfig.com/tpb/search?id=".str_replace("%26%23x26%3B","%26",str_replace("%26%23x27%3","%27",rawurlencode($term))."&key=SNIPPED"),false,$tctx));
		if (count($results)>0) {
			foreach ($results as $result) {
				if ($result->category==$category || $category==null || $result->category==$category2) {
					$top = $results[0];
					return $top->magnet;
				}
			}
		}
		
		return null;
	}
	
	function getTrailer($term) {
		return "http://youtube.com";
	}
	
	function getPercentile($id) {
		$rpc = new TransmissionRPC();
		
		$tmedia = $rpc->get((int)$id,array('id','name','percentDone','files'));
		if (!isset($tmedia->arguments->torrents[0]->percentDone))
			return 0;
		return floor($tmedia->arguments->torrents[0]->percentDone*100);
	}
	
	function getFiles($id) {
		$rpc = new TransmissionRPC();
		$return = array();
		
		$tmedia = $rpc->get((int)$id,array('id','name','percentDone','files'));
		foreach ($tmedia->arguments->torrents[0]->files as $file)
			$return[] = $file->name;
		return implode(";",$return);
	}
	
	function removeTor($id) {
		$rpc = new TransmissionRPC();
		
		$rpc->remove((int)$id);
	}
	
	function getSize($id) {
		$rpc = new TransmissionRPC();
		$return = 0;
		
		$tmedia = $rpc->get((int)$id,array('id','name','percentDone','files'));
		foreach ($tmedia->arguments->torrents[0]->files as $file)
			$return += $file->length;
		return $return;
	}
	
	function addMagnet($magnet) {
		$rpc = new TransmissionRPC();
		$add_result = $rpc->add($magnet);
		
		return $add_result->arguments->torrent_added->id;
	}
?>
