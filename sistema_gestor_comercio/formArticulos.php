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
        $articulos = new Articulos();

        session_start();
        if (!isset($_SESSION['login'])) {
            header('Location: index.php');
          } else{
            $login = $_SESSION["login"];
          }
        
        if($log->compruebaPermisoUsuario($login) || $log->compruebaSuperadmin($login)){
        }else{
            header("Location:indexX.php");
        }
        
    ?> 

    <section class="center">

        <?php

            if(isset($_GET['editar'])){
                $id = $_GET['editar'];
                $productos = $articulos->getArticulo($id);
                echo "<h2>Se va a editar un artículo nuevo</h2><br>";

            }elseif(isset($_GET['borrar'])){
                $id = $_GET['borrar'];
                $productos = $articulos->getArticulo($id);
                echo "<h2>Se va a borrar un artículo nuevo</h2><br>";

            }elseif(isset($_GET['crear'])){ 
                $productos =[
                    "ProductID" => "",
                    "Name" => "",
                    "Cost" => "",
                    "Price" => "",
                    "CategoryID" => ""];
                echo "<h2>Se va a añadir un artículo nuevo</h2><br>";
            } 

        ?> 
 
        <?php // Mostrar accion realizada o formulario.
            if(!empty($_POST['id'])){
            $id = $_POST["id"];
            $categoria = $_POST["categoria"];
            $nombre = $_POST["nombre"];
            $coste = $_POST["coste"];
            $precio = $_POST["precio"];
            }

            if(isset($_POST['anadir'])){
                $articulos->crearArticulo($id, $nombre, $coste, $precio, $categoria); 
            }elseif(isset($_POST['editar'])){ 
                $articulos->editarArticulo($id, $nombre, $coste, $precio, $categoria);  
            }elseif(isset($_POST['borrar'])){ 
                $articulos->borrarArticulo($id); 
            }else{?>

                <form action="formArticulos.php" method="POST">
                    <label for="id">ID:</label>
                    <input type="number" name="id" id="id" value=<?php echo $productos['ProductID'];?>>
                    <label for="categoria">Categoría:</label> 
                    <select name="categoria" id="categoria">
                        <option value="">Elige una categoria</option>
                        <?php
                        $articulos->showListaCategorias($productos["CategoryID"]);
                        ?>
                    </select>                   
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo $productos['Name'];?>">
                    <label for="coste">Coste:</label>
                    <input type="number" name="coste" id="coste" value=<?php echo $productos['Cost'];?>>
                    <label for="precios">Precio:</label>
                    <input type="number" name="precio" id="precio" value=<?php echo $productos['Price'];?>>
 
                    <?php // Mostramos boton segun de donde vengamos (editar, borrar o crear).
                        if (isset($_GET['editar'])){
                            echo "<a class='btn-rtn' href='articulos.php'>Volver</a>
                            <button class='btn-form' input type='submit' name='editar' value='Editar'>Editar</button>";
                        }else if(isset($_GET['borrar'])){
                            echo "<a class='btn-rtn' href='articulos.php'>Volver</a>
                            <button class='btn-form' input type='submit' name='borrar' value='Borrar'>Borrar</button>";
                        }else{
                            echo "<a class='btn-rtn' href='articulos.php'>Volver</a>
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