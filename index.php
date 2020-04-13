<?php

require_once('utils/autoload.php');

if (session_status() == PHP_SESSION_NONE) { //se la sessione non è avviata la avvio
    session_start();
}

if (isset($_REQUEST['controller'])) {
    $controller = $_REQUEST['controller'];
} else {
    $controller = 'utentiController';
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = 'visualizzaLogin';
}

$controllerObj = new $controller();
$controllerObj->$action(); 

?>
