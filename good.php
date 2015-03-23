<?php 
if(isset($_GET["varname"])){
	$thenumber = $_GET["varname"];
} else{
	$thenumber = 2;
}
echo "<script>var number = "; 
echo $thenumber;
echo "</script>";
?>

