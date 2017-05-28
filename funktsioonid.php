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
						$_SESSION['id'] = $user_id;
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

function kuva_pildid($kategooria){
	global $connection;
	$pildid=array();
	if ($kategooria=='koik'){
		$category='k&#245;ik';
		$pildid=mysqli_query($connection, "SELECT * FROM pulmas_galerii");
	} else {
		$category=$kategooria;
		$pildid=mysqli_query($connection, "SELECT * FROM pulmas_galerii WHERE category='".$kategooria."'");
	}
		
	include_once('vaated/galerii.html');
	
}

function lisa_pilt($lisaja){
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//kui meetodiks oli POST, tuleb kontrollida, kas kõik vormiväljad olid täidetud ja tekitada vajadusel vastavaid veateateid (massiiv $errors). 
		$errors = array();
  	
  		if(empty($_POST['category'])) {
	    	$errors[] = "kategooria on puudu";
		}
				
		$pilt = upload("title");
		if ($pilt == "") {
			$errors[] = "pilt on puudu";
		}
	  	if (empty($errors)) {
	  		//Kui vigu polnud, siis üritada see pilt andmebaasitabelisse lisada. 
	  		global $connection;
			$category = mysqli_real_escape_string($connection, $_POST["category"]);
			$title = $pilt;
	  		$link = $category."/".$title;
			
			$sql = "SELECT id FROM pulmas_galerii_users WHERE username='$lisaja'";
			$tulem = $connection->query($sql);
			$row = $tulem->fetch_assoc();
			if ($tulem->num_rows > 0) {
				$user_id = $row["id"];
			}
			$query = "INSERT INTO pulmas_galerii (user_id, category, title, link) VALUES (".$user_id.", '".$category."', '".$title."', '".$link."')";
			$result = mysqli_query($connection, $query) or die("midagi läks valesti, query: ".$query."");
		
			//Kas pildi lisamine õnnestus või mitte, saab teada kui kontrollida mis väärtuse tagastab mysqli_insert_id funktsioon. Kui väärtus on nullist suurem, suunata kasutaja galerii vaatessse 
			if (mysqli_insert_id($connection) > 0) {
				header("Location: ?page=galerii");
			}
	  	} 
	}
	include_once('vaated/lisamine.html');
}

function upload($title){
	$category = $_POST['category'];
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	$allowedTypes = array("image/gif", "image/jpeg", "image/png","image/pjpeg");
	if ( in_array($_FILES[$title]["type"], $allowedTypes)
		&& ($_FILES[$title]["size"] < 10000000)) {
    // fail õiget tüüpi ja suurusega
		if ($_FILES[$title]["error"] > 0) {
			$_SESSION['notices'][]= "Return Code: " . $_FILES[$title]["error"];
			return "";
		} else {
      // vigu ei ole
			if (file_exists("pildid/".$category."/". $_FILES[$title]["name"])) {
        // fail olemas ära uuesti lae, tagasta failinimi
				$_SESSION['notices'][]= $_FILES[$title]["name"] . " juba eksisteerib. ";
				return "pildid/".$category."/".$_FILES[$title]["name"];
			} else {
        // kõik ok, aseta pilt
				move_uploaded_file($_FILES[$title]["tmp_name"], "pildid/".$category."/". $_FILES[$title]["name"]);
				return $_FILES[$title]["name"];
			}
		}
	} else {
		return "midagi sassis";
	}
}

function punkt($pildi_id){
	//if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['annanPunkti'])){punkt($_POST["id"]);}

	global $connection;
	$sql="UPDATE pulmas_galerii SET votes=votes+ WHERE id=".$pildi_id."";
	//mysqli_query($connection, "UPDATE pulmas_galerii SET votes=votes+1 WHERE id=".$pildi_id."");
	if (mysqli_query($connection, $sql)) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . mysqli_error($connection);
	}
	include_once('vaated/galerii.html');

}
	

?>