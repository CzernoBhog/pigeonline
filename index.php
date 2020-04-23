<?php

// use controllers\utentiController;    // Decomentare se da errori per namespace

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

// Viene controllato se l'utente è autenticato o meno
if (empty($_SESSION)) {
    switch($action){        // Queste 3 pagine non necessitano di autenticazione, quindi posso essere aperte
        case "viewRegistration":
        case "viewLogin":
        case "viewFirstPage":
            (new controllers\utentiController)->$action();
            exit;

        default:        // Viene reindirizzato alla firstPage se non autenticato
            (new controllers\utentiController)->viewFirstPage();
            exit;
    }
}

// Viene richiamata l'azione dell'oggetto (l'oggetto dura solo per la durata dell'espressione)
(new $controller)->$action();

// $controllerObj = new $controller($action);

?>
