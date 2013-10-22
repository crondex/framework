<?php

//DisplayProfiles.php

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

