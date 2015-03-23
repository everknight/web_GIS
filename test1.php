<?php
ini_set('display_errors', 'On');
// Create connection
$con=mysqli_connect("webapps2-db.miserver.it.umich.edu","cehiwebgis","123456cehi","cehiwebgis");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
} else {

  #phpinfo();
  if(isset($_GET["field"]))$field = $_GET["field"];
  
  $result = mysqli_query($con,'SELECT GEOID10, '.$field.' FROM npi_demo');
  echo '[';
  $first = True;
  while($row = mysqli_fetch_array($result)){
	if($first == True){
		$first = False;
	} else {
		echo ',';
	}
	//$json = json_encode($row);
	//echo $json;
	$arrkey = array_keys($row);	
	echo '{"'.array_pop($arrkey).'":"';
	echo array_pop($row).'",';
	echo '"'.array_pop($arrkey).'":"';
	echo array_pop($row).'",';
	echo '"'.array_pop($arrkey).'":"';
	echo array_pop($row).'",';
	echo '"'.array_pop($arrkey).'":"';
	echo array_pop($row).'"}';
  }
  echo ']';
  
}
?>