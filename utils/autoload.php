<?php

//autoloading delle classi attraverso i namespace e la funzione che viene richiamata ad ogni 'new'
spl_autoload_register(
    function ($name){
        require_once str_replace("\\", "/", $name) . ".php";
    }
);

?>