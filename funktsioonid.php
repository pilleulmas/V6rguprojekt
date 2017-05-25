<?php

function connect_db(){
	global $connection;
	$host="localhost";
	$user="test";
	$pass="t3st3r123";
	$db="test";
	$connection = mysqli_connect($host, $user, $pass, $db) or die("ei saa ühendust mootoriga- ".mysqli_error());
	mysqli_query($connection, "SET CHARACTER SET UTF8") or die("Ei saanud baasi utf-8-sse - ".mysqli_error($connection));
}

function login(){
	if (isset($_POST['user'])) {
		include_once('vaated/galerii.html');
	}
	if (isset($_SERVER['REQUEST_METHOD'])) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		  	
		  	$errors = array();
		  	if (empty($_POST['user']) || empty($_POST['pass'])) {
		  		if(empty($_POST['user'])) {
			    	$errors[] = "kasutajanimi on puudu";
				}
				if(empty($_POST['pass'])) {
					$errors[] = "parool on puudu";
				} 
		  	} else {
		  		global $connection;
		  		$username = mysqli_real_escape_string($connection, $_POST["user"]);
		  		$passw = mysqli_real_escape_string($connection, $_POST["pass"]);
		  		
				$query = "SELECT id FROM pulmas_galerii_users WHERE username='$username' && password=SHA1('$passw')";
				$result = mysqli_query($connection, $query) or die("midagi läks valesti");
			
				$ridu = mysqli_num_rows($result);
					if ( $ridu > 0) {
						$_SESSION['user'] = $username;
						header("Location: ?page=galerii");
					}
		  	}
		//igasuguste vigade korral ning lehele esmakordselt saabudes kuvatakse kasutajale sisselogimise vorm failist login.html
		} else {
			 include_once 'vaated/login.html';
		}
	}
	
	include_once('vaated/login.html');
}

function logout(){
	$_SESSION=array();
	session_destroy();
	header("Location: ?");
}

function kuva_pildid(){
	//Kontrollib, kas kasutaja on sisse logitud. Kui pole, suunab sisselogimise vaatesse
	if (!empty($_SESSION['user'])) {
		global $connection;
		$p= mysqli_query($connection, "select distinct(category) as category from pulmas_galerii");
		$category=array();
		while ($r=mysqli_fetch_assoc($p)){
			$l=mysqli_query($connection, "SELECT * FROM pulmas_galerii WHERE  category=".mysqli_real_escape_string($connection, $r['category']));
			
		}
		include_once('vaated/galerii.html');
	} else {
		include_once 'vaated/login.html';
	}
	
}

?>