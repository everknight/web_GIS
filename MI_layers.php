<?php 
if(isset($_GET["layer"])){
	$thefield = $_GET["layer"];
} else{
	$thefield = "Pop_den";
}
echo "<script>var thefield = '"; 
echo $thefield;
echo "'</script>";

ini_set('display_errors', 'On');
// Create connection
$con=mysqli_connect("webapps2-db.miserver.it.umich.edu","cehiwebgis","123456cehi","cehiwebgis");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
} else {

  #phpinfo();
  $mi = file_get_contents('../web_GIS/MI_Tract.js');
  $data = explode('"GEOID10":', $mi);
  $i = 0;
  foreach ($data as &$dt){
	if($i == 0){
	  echo $dt.'"GEOID10":';
	} else {
	  $id = explode(', "NAME10":', $dt);
	  echo '<br>'.$dt.'<br>';
	  $result = mysqli_query($con,'SELECT GEOID10, '.$thefield.' FROM npi_demo WHERE GEOID10 = '.current($id));
	  while($row = mysqli_fetch_assoc($result)) {
		echo '<br>';
		echo implode(" ", $row);
		echo '<br>';
	  }
	}
	$i = $i + 1;
  }
  
  #echo '<p>SELECT GEOID10, '.$thefield.' FROM npi_demo <br></p>';
  
  
}
?>

<script src='../web_GIS/MI_Tract.js'></script>
