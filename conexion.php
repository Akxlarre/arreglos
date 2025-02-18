<?php
function bdconerctor($bbdd=''){
    $_bbdd = 'cloux';
    if($bbdd!=''){
        $_bbdd = $bbdd;
    }

    $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD',$_bbdd);
    /*$link = mysqli_connect("localhost", "std_int",'nVbKdX4kLd36MirY',$_bbdd);*/

    if (mysqli_connect_errno()) {
        printf("Falló la conexión: %s\n", mysqli_connect_error());
        exit();
    }
    mysqli_set_charset($link, "utf8");
    return $link;
}
$link=bdconerctor();

?>