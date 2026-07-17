<?php

session_start();

if (!isset($_SESSION["id_utilizador"])) {

    header("Location: /pap12etiagocastro/login.php");
    exit();

}

?>