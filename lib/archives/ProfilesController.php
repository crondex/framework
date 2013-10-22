<?php

//Presenter

class ProfileController {

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
		$displaySettings->setDisplaySql(' FROM staff_profiles LEFT OUTER JOIN images on staff_profiles.id = ?');
	
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
		$params = array('images.id');
		$displayProfiles->rows = $db->query($displaySettings->sql, $params, 'names');
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
