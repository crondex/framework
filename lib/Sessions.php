
<?php

//Chris' new session handler (from this gist: https://gist.github.com/PureForm/c392aa219a157528e4af)	
class SessionManager { # UTILITY / LIBRARY / SERVICE ABSTRACTION

	public $lifeTime;
 
	public function __construct() {
		
		global $_GLOBALS;
 
		$this->lifeTime = (int) ini_get('session.gc_maxlifetime'); //https://blogs.oracle.com/oswald/entry/php_session_gc_maxlifetime_vs
 
		session_set_cookie_params(0,'/',$_GLOBALS['cookie-domain'],false,true);
		session_name('TESTSESSION');
		session_set_save_handler(array (&$this,'open'),array (&$this,'close'),array (&$this,'read'),array (&$this,'write'),array (&$this,'destroy'),array (&$this,'gc'));
		session_start();
	}
 
	public function open($savePath,$sessionName) {
		return true;
	}
 
	public function close() {
		return true;
	}
 
	public function read($sessionID) {
		$data = $this->memcache->get($sessionID);
		if ($data === false) {
 			$this->memcache->set($sessionID,$data,false,$this->lifeTime);
 		}	


		# The default miss for MC is (bool) false, so return it
		return MC::get('user-session_' . $sessionID);
	}
 
	public function write($sessionID,$data) {
		# This is called upon script termination or when session_write_close() is called, which ever is first.
		return MC::set('user-session_' . $sessionID,$data,$this->lifeTime,true);
	}
 
	public function destroy($sessionID) {
		# Called when a user logs out...
		return MC::delete('user-session_' . $sessionID);
	}
 
	public function gc($maxlifetime) {
		# The MC keys expire on their own, no need to do anything here.
		return true;
	}
}

$test = new SessionManager;

echo "this is a test:" . $test->read(12345);

//Chris' old session handler from his blog
/*
class SessionHandlerOld {
	public $lifeTime;
	public $memcache;
	public $initSessionData;

	function __construct() {
		register_shutdown_function("session_write_close");

		$this->memcache = new Memcache;
		$this->lifeTime = intval(ini_get("session.gc_maxlifetime"));
		$this->initSessionData = null;
		$this->memcache->connect("127.0.0.1",11211);

		return true;
	}

	function open($savePath,$sessionName) {
		$sessionID = session_id();
		if ($sessionID !== "") {
			$this->initSessionData = $this->read($sessionID);
		}
		return true;
        }

	function close() {
		$this->lifeTime = null;
		$this->memcache = null;
		$this->initSessionData = null;

		return true;
	}

	function read($sessionID) {
            $data = $this->memcache->get($sessionID);
            if ($data === false) {
                # Refresh MC key: [Thanks Cal :-)]
                $this->memcache->set($sessionID,$data,false,$this->lifeTime);
            }

            # The default miss for MC is (bool) false, so return it
            return $data;
        }

        function write($sessionID,$data) {
		# This is called upon script termination or when session_write_close() is called, which ever is first.
		$result = $this->memcache->set($sessionID,$data,false,$this->lifeTime);
		return $result;
	}

	function destroy($sessionID) {
		# Called when a user logs out...
		$this->memcache->delete($sessionID);
		return true;
	}

	function gc($maxlifetime) {
		return true;
	}
}

    ini_set("session.gc_maxlifetime",60 * 30); # 30 minutes
    session_set_cookie_params(0,"/",".myapp.com",false,true);
    session_name("MYAPPSESSION");
    $sessionHandler = new SessionHandler();
    session_set_save_handler(array (&$sessionHandler,"open"),array (&$sessionHandler,"close"),array (&$sessionHandler,"read"),array (&$sessionHandler,"write"),array (&$sessionHandler,"destroy"),array (&$sessionHandler,"gc"));
    session_start();
*/
?>
