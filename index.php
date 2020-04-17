<?php

require_once('utils/autoload.php');
// here
/* if (session_status() == PHP_SESSION_NONE) { //se la sessione non è avviata la avvio
    session_start();
} */

$waf = new utils\WAF();
//$waf->getCSRF();
//$waf->start();  //controllo con WAF che non si tenti di passare parametri/cookie malevoli

if (isset($_REQUEST['controller'])) {
    $controller = 'controllers\\' . $_REQUEST['controller'];
} else {
    $controller = 'controllers\utentiController';
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = 'viewFirstPage';
}

$controllerObj = new $controller();
$controllerObj->$action(); 

?>
