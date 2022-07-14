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

    <section class="center">

        <?php 

            if(isset($_GET['editar'])){
                $user_id = $_GET['editar'];
                $usuarios = $users->getUsuarios($user_id);  
                echo "<h2>Se va a editar un usuario nuevo</h2><br>";
                
            }elseif(isset($_GET['borrar'])){
                $user_id = $_GET['borrar'];
                $usuarios = $users->getUsuarios($user_id);
                echo "<h2>Se va a borrar un usuario nuevo</h2><br>";

            }elseif(isset($_GET['crear'])){ // Si vengo de añadir, los valores se quedan vacíos.
                $usuarios =[
                    "UserID" => "",
                    "FullName" => "",
                    "Password" => "",
                    "Email" => "",
                    "LastAccess" => "",
                    "Enabled" => ""];
                echo "<h2>Se va a añadir un usuario nuevo</h2><br>";
            } 

        ?>

    <?php // Mostrar accion realizada o formulario.
            if(!empty($_POST['id'])){
            $id = $_POST["id"];
            $nombre = $_POST["name"];
            $pass = $_POST["pass"];
            $email = $_POST["email"];
            $acceso = $_POST["access"]; 
            $enabled = $_POST["enabled"];
            }

            if(isset($_POST['anadir'])){
                $users->crearUsuario($id, $nombre, $pass, $email, $acceso, $enabled); 
                
            }elseif(isset($_POST['editar'])){ 
                $users->editarUsuario($id, $nombre, $pass, $email, $acceso, $enabled); 
                    
            }elseif(isset($_POST['borrar'])){ 
                $users->borrarUsuario($id); 
            }else{?>

                <form action="formUsuarios.php" method="POST">
                    <label for="id">ID: </label>
                    <input type="number" name="id" id="id" value=<?php echo $usuarios['UserID'];?>>
                    <label for="name">Nombre: </label> 
                    <input type="text" name="name" id="name" value="<?php echo $usuarios['FullName'];?>">               
                    <label for="pass">Password: </label>
                    <input type="password" name="pass" id="pass" value=<?php echo $usuarios['Password'];?>>
                    <label for="email">Email: </label>
                    <input type="email" name="email" id="email" value=<?php echo $usuarios['Email'];?>>
                    <label for="email">Último Acceso: </label>
                    <input type="date" name="access" id="access" value=<?php echo $usuarios['LastAccess'];?>>      
                    <label for="enabled">Autorizado: </label><br>                  
                    <label for="enabled">Sí</label>   
                    <input type="radio" name="enabled" id="enabled" value="1" <?php if ($usuarios['Enabled']=='1') echo 'checked';?>>                                                                   
                    <label for="enabled">No</label>
                    <input type="radio" name="enabled" id="enabled" value="0" <?php if ($usuarios['Enabled']=='0') echo 'checked';?>>
                    
                    <?php // Mostrar botón seguún de donde vengamos (editar, borrar o añadir).
                        if (isset($_GET['editar'])){
                            echo "<a class='btn-rtn' href='usuarios.php'>Volver</a>
                            <button class='btn-form' input type='submit' name='editar' value='Editar'>Editar</button>";
                        }else if(isset($_GET['borrar'])){
                            echo "<a class='btn-rtn' href='usuarios.php'>Volver</a>
                            <button class='btn-form' input type='submit' name='borrar' value='Borrar'>Borrar</button>";
                        }else{
                            echo "<a class='btn-rtn' href='usuarios.php'>Volver</a>
                            <button class='btn-form' input type='submit' name='anadir' value='Añadir'>Añadir</button>";
                        }
                    ?>
                    
                </form>
                
        <?php
            }
        ?>

</section>

</body>
</html>