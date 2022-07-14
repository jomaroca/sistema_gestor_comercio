<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
    <title>Comercio</title>
</head>
<body>

    <?php

        include "baseDatos.php";

        $log = new Login();

        session_start();

        if (!isset($_SESSION['login'])) {
            header('Location: index.php');
          } else{
            $login = $_SESSION["login"];
            $user = $_SESSION["user"]; 
        }

    ?>

    <section class="center">

        <h2>Bienvenido <?=$user?></h2>

    </section>

    <div class="container">

        <a class="btn-stn" href="articulos.php">Artículos</a> 

       <?php
            if($log->compruebaSuperadmin($login)){
                echo "<a class='btn-stn' href='usuarios.php'>Usuarios</a>";
            }
        ?>
        
    </div>

    <section class="top">

    <a class="boton" href="index.php">Volver</a>

    </section>
    
</body>
</html>