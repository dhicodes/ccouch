<?php
/*****
 * Class Couch core definition
 * init as $obj = new Ccounch($options)
 * $options array
 * proto   => "http"
 * host    => "127.0.0.1"
 * port 	 => "5984"
 * dbase	 => "circa3_quest"
 * user    => "user"
 * pass    => "pass"
 *
 * provides CRUD functions
 * create($doc)
 * read($id)
 * update($id, $updates)
 * delete($id)
 * 
 * $doc can be JSON or associative array
 *
 * each function returns JSON results
 * "OK":"true" if success
 *
 * (c) dhi 2020
 */

Class Ccouch {
	function __construct($options) {
			foreach($options as $key => $value) {
				$this->$key = $value;
			}
	}

	public function send($method, $url, $post_data = NULL) {

		$header = array(
		'Content-type: application/json',
		'Accept: */*');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); /* or PUT */
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->pass);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	public function create($doc = null) { //=============================================
	// creates document, accepts doc as json or array
	// returns status in json format

		//check if doc is array
		if (!is_array($doc)) {
			$doc = json_decode($doc, true);
		}
		// data check
		foreach ($doc as $key=>$value) {
			if ($value != null && $value != '') {
				$checkedDoc[$key] = $value;
			}
		}
		unset($checkedDoc['_rev']);
		if (isset($checkedDoc['_id'])) {
				$id = $checkedDoc['_id'];
		} else {
				$id = uniqid();
				$checkedDoc['_id'] = $id;
		}

	    // data definition
	    $data = json_encode($checkedDoc);
	    $url = $this->proto.'://'.$this->host.':'.$this->port.'/'.$this->dbase.'/'.$id;
	    return $this->send("PUT", $url, $data);;
	}

	public function read($id = NULL, $rev=NULL) { //=====================================
	// gets id
	// returns json

		$url = $this->proto.'://'.$this->host.':'.$this->port.'/'.$this->dbase."/".$id;
		if (isset($rev)) {
			$url .= "?rev=$rev";
		}

		$resp = $this->send('GET', $url);
		//print_r($resp);
		return $resp;
		//return json_decode($resp, true);
	}

	public function update($doc=null) { //=============================================
		//update doc
		//returns json answer
			if (!is_array($doc)) {
				$doc = json_decode($doc, true);
			}

	    if (!isset($doc['_id'])) {
			$doc['_id'] = $doc['id'];
	    }
		$id = $doc['_id'];
		$asis = json_decode($this->read($doc['_id']), true);
    	// mv $doc in $asis
		$asis = array_replace($asis, $doc);

     	data = json_encode($asis);
      	$url = $this->proto.'://'.$this->host.':'.$this->port.'/'.$this->dbase.'/'.$id;
	    $response = $this->send("PUT", $url, $data);

		return $response;
	}

	function delete($id=null) { //==============================================
 	//delete $id
		$doc = json_decode($this->read($id), true);
		$rev = $doc['_rev'];

		$url = $this->proto.'://'.$this->host.':'.$this->port.'/'.$this->dbase."/".$id."?rev=".$rev;
		$response = $this->send("DELETE", $url);
		return $response;
 	}
}
?>
