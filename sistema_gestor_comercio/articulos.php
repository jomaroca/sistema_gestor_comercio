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
        
        // Comprobar usuarios.
        if($log->compruebaPermisoUsuario($login) || $log->compruebaSuperadmin($login)){
            echo  "<a class='boton' href='formArticulos.php?crear='>Crear nuevo artículo</a>";
        }

        // Mostrar artículos.
        $articulos = new Articulos();
        $articulos->listadoArticulos($login);
        
    ?>

    <a class="boton" href="acceso.php">Volver</a>
      
</body>
</html>