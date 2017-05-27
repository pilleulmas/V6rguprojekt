<?php
require_once('funktsioonid.php');
session_start();
connect_db();

$page="pealeht";
include_once('vaated/head.html');

if (isset($_GET['page']) && $_GET['page']!=""){
	$page=htmlspecialchars($_GET['page']);
}


switch($page){
	case "pealeht":
		include('vaated/pealeht.html');
	break;
	case "galerii":
		kuva_pildid('koik');
	break;
	case "loodus":
		kuva_pildid('loodus');
	break;
	case "makro":
		kuva_pildid('makro');
	break;
	case "tehnika":
		kuva_pildid('tehnika');
	break;
	case "login":
		login();
	break;
	case "logout":
		logout();
	break;
	case "lisa":
		lisa_pilt($_SESSION['user']);
	break;
	default:
		include_once('vaated/pealeht.html');
	break;
}


include_once('vaated/foot.html');

?>