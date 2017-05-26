<?php
require_once('funktsioonid.php');
session_start();
connect_db();

$page="pealeht";
if (isset($_GET['page']) && $_GET['page']!=""){
	$page=htmlspecialchars($_GET['page']);
}

include_once('vaated/head.html');

switch($page){
	case "pealeht":
		include('vaated/pealeht.html');
	break;
	case "galerii":
		kuva_pildid('kõik');
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
	default:
		include_once('vaated/algus.html');
	break;
}

include_once('vaated/foot.html');

?>