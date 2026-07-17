<?php
require_once __DIR__ . "/../includes/autenticacao.php";
exigirAdministrador();
redirecionar("admin/dashboard.php");
