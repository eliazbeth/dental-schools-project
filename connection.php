<?php 
	$host = 'localhost';
	$data = 'dentalschools';
	$user = 'liz';
	$pass = 'pwpw';
	$chrs = 'utf8mb4';
	$attr = "mysql:host=$host;dbname=$data;charset=$chrs";
	$opts =
	[
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	];	
    if(!($con = mysqli_connect($host,$user,$pass,$data)))
    {
        die("Failed to connect to database.");
    }
?>