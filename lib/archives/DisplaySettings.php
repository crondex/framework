<?php

//DisplaySettings.php

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
