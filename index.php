<?php

require_once('utils/autoload.php');

$waf = new utils\WAF();     // Fa partire il Web Application Firewall

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
