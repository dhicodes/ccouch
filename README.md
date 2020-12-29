# Diwan
Simple PHP Interface to CouchDB with curl as HTTP agent

/*
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
