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
    <section class="center">

        <h1>Bienvenido a la Plataforma</h1>

        <form action="index.php" method="POST">
            <label for="usuario">Usuario: </label>
            <input type="text" name="usuario" id="usuario">
            <label for="Email">Email: </label>
            <input type="email" name="email" id="email">
            <input class="btn-log" type="submit" name="login" id="login" value="Acceder">
        </form> 

    </section>

        <?php 

            include "baseDatos.php";  

            $log = new Login();

            if(isset($_POST["login"])){ 
                if ($log->comprobarLogin($_POST["usuario"], $_POST["email"]) ){
                    echo "<div class='login-msg'><p>Bienvenido " . $_POST["usuario"] . ", pulse <a href='acceso.php'>AQUÍ</a> para continuar.</p></div>";
                }else{
                    echo "<div class='login-msg'><p>Ha introducido un usuario o email incorrectos.</p></div>";
                }
            } 

        ?>
     
</body>
</html>