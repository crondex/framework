<?php

//$data_array = $fruit->fetchAll();
//print_r($data_array);

//echo '<pre>';
//this is an object
//var_dump($fruit);

//to call only one cell from one column (uncomment two lines)
//$test=$fruit->fetchall();
//print_r($test[1]['fruit_name']);

//this sort of works, but only works with the index 1
//echo $fruit->fetch(1)->fruit_name;
//echo '</pre>';

echo "<hr />";

//print_r($fruit);
foreach($fruit as $piece) {
        echo '<pre>';

        //to call a specific column
        echo $piece['fruit_name'];

        //to call the whole array
	//print_r($piece);

        echo '</pre>';
}

?>
 
