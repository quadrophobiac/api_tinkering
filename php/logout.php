<?php
session_start();
    session_destroy();
    if($_SERVER["REQUEST_METHOD"] == "GET"){
    	$url = $_GET['dest'];
    	//echo $url;
    	// $url = 'min.php';
		header('Location: '.$url.'.php');
	}
?>