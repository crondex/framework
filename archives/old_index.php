<?php

require_once('config.php');
require_once('autoloader.php');

$authenticated = TRUE;
if ($authenticated) {
	//do stuff
	try {
		$db = new Database($config);
		print_r($_GET);
		$router = new Router;
		$router->doUserAction($db);

	} catch (Exception $Exception) {
		echo 'Oops! ' . $Exception->getMessage();
	}
} else {
	echo "Please login:";
	echo "Username:<br />";
	echo "Password:<br />";
	echo "Don't have a login? Create an account.";
}

class Router {

	public $db;

	function doUserAction($db) {

		if (!isset($_GET['action']))
		return;
		//Class member access on instantiation is available in PHP 5.4
		//(new Presenter())->$_GET['action']();

		$presenter = new Presenter();
		$presenter->$_GET['action']($db);

	}
}

class Presenter {

	function hello() {
		echo "<br />Hello World!<br />";
	}

	function profiles($db) {

		//create objects
		$displaySettings = new DisplaySettings($db);
		$displayProfiles = new DisplayProfiles();

		//set sql
		$displaySettings->setDisplaySql('SELECT DISTINCT staff_profiles.id,');
		$displaySettings->setDisplaySql('images.image_path,');
		$displaySettings->setDisplaySql('staff_profiles.name,');
		$displaySettings->setDisplaySql('staff_profiles.position,');
		$displaySettings->setDisplaySql('staff_profiles.type,');
		$displaySettings->setDisplaySql('staff_profiles.phone,');
		$displaySettings->setDisplaySql('staff_profiles.email,');
		$displaySettings->setDisplaySql('staff_profiles.details,');
		$displaySettings->setDisplaySql('staff_profiles.visible');
		$displaySettings->setDisplaySql(' FROM staff_profiles LEFT OUTER JOIN images on staff_profiles.id = images.id');

		//set columns
		$displaySettings->setSqlColumns('image_path');
		$displaySettings->setSqlColumns('name');
		$displaySettings->setSqlColumns('position');
		$displaySettings->setSqlColumns('type');
		$displaySettings->setSqlColumns('phone');
		$displaySettings->setSqlColumns('email');
		$displaySettings->setSqlColumns('details');
		$displaySettings->setSqlColumns('visible');

		//transfer data and settings to $displayProfiles class
		//the 2nd argument sets the fetch mode ('numbers','names', or 'both'), determining how the array from $db is returned
		$displayProfiles->rows = $db->query($displaySettings->sql, 'names');
		$displayProfiles->columns = $displaySettings->columns;

		//print data
		$displayProfiles->printUserInfo();
	}
	
	public function delete($db) {
		//this worked //could use a try/catch to catch exceptions
		$db->query("DELETE from staff_profiles where name='Andrew McLaughlin'");
	}

	public function test($db) {
		$sql = "INSERT INTO staff_profiles (name, position, type, phone, email, details, visible) VALUES (?,?,?,?,?,?,?)";
		$params = array('Andrew McLaughlin', 'Kindergarten', 'classified', '5034082742', 'andrew@parkrose.k12.or.us', 'can jump', '1'); 
		$db->query($sql, $params);
	}
}

class DisplaySettings {

	//sets MySQL query and defines which columns to evalute

	public $sql;
	public $columns;

	public function __construct() {
		$this->columns = array();
	}

	//creates the sql query
	public function setDisplaySql($queryPart){
		$this->sql .= $queryPart;
 	}

	//defines which database columns to evaluate
	public function setSqlColumns($newColumn) {
		array_push($this->columns, $newColumn);
 	}
}

class DisplayProfiles {

	public $rows;
	public $columns;

	public function printUserInfo() {

		//print_r($this->rows);		//debugging
		//print_r($this->columns);	//debugging

		//for each row in the query results
		foreach ($this->rows as $row) {

			echo "<pre>";  //debugging
			print_r($row); //debugging
			echo "</pre>"; //debugging

			//for each column
			for ($j=0; $j < count($this->columns); ++$j) {

				(isset($row[$this->columns[$j]])) ? $column = $row[$this->columns[$j]] : $column='';
				echo "<div class=\"" . $this->columns[$j] . "\">" . $column . "</div>";
			}

			echo "<div class=\"actions\"></div>";
		}
	}
}

?>
