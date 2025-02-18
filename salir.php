<?php
// include("conexion.php");
// include("funciones.php");
session_start();
unset($_SESSION["codsession"]);
unset($_SESSION["cloux_new"]);
session_unset();
session_destroy();
header("Location:index.php");
?>