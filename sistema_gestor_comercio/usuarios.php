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

        $users = new Users();

        session_start();

        if (!isset($_SESSION['login'])) {
            header('Location: index.php');
          } else{
        } 
        
    ?>

        <a class='boton' href='formUsuarios.php?crear='>Crear nuevo usuario</a>

    <?php 

        $users->listadoUsuarios();
        
    ?>
    
    <a class="boton" href="acceso.php">Volver</a>  

</body>
</html>