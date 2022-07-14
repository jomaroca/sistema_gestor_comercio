<?php

    define("DB_HOST", "localhost");
    define("DB_USER", "root");
    define("DB_PASS", "");
    define("DB_NAME", "pac3_daw");
    
    // Conexión con la base de datos.
    Class Connection{
        
        protected $conn_db;

        public function __construct(){
            try{
                $this->conn_db = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_NAME.'', DB_USER, DB_PASS);
                $this->conn_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $this->conn_db; 
            }catch(Exception $e){
                echo "Error: " . $e->getLine();
            }

            if($this->conn_db->connect_errno){
                echo "Fallo al conectar con la base de datos: " . $this->conn_db->connect_error;
                return;
            } 
        }
    }

    // Consultas para identificación de usuarios.
    class Login extends Connection{

        public function __construct(){
            parent::__construct();
        }

        public function comprobarLogin($usuario, $email){

            $sql = "SELECT * FROM user WHERE FullName=? AND Email=?";
    
            $stmt=$this->conn_db->prepare($sql);
            $stmt->execute([$usuario, $email]);
    
            if($stmt->fetch()){ 
                session_start();
                $_SESSION["login"] = $email;
                $_SESSION["user"] = $usuario;
                $this->ultimoAcceso($email);
                return true;
    
            }else{ 
                return false;
            }
        } 
      
        public function ultimoAcceso($email){

            $sql = "UPDATE user SET LastAccess = NOW() WHERE Email=?";
            $stmt=$this->conn_db->prepare($sql);
            $stmt->execute([$email]);  
        }
      
        public function compruebaSuperadmin($login){

            $sql= "SELECT SuperAdmin FROM setup";
            $stmt=$this->conn_db->query($sql);
            $superadmin=$stmt->fetchColumn(); 
    
            $sql="SELECT `UserID` FROM `user` WHERE `Email`=?";
            $stmt=$this->conn_db->prepare($sql);
            $stmt->execute([$login]);
            $user_id=$stmt->fetchColumn(); 
    
            if($superadmin == $user_id){
                return true;
            }else{
                return false;
            }
        }
       
        public function compruebaPermisoUsuario($login){

            $sql ="SELECT Enabled FROM user WHERE Email=?";
            $stmt=$this->conn_db->prepare($sql);
            $stmt->execute([$login]);
     
            if($stmt->fetchColumn() == 1){
                return true;
            }else{
                return false;
            }
        }
    }

    // Consultas para lista artículos.
    class Articulos extends Connection{
         
        public function __construct(){

            parent::__construct();
        } 

        public function getArticulo($id){

            $sql = "SELECT ProductID, Name, Cost, Price, CategoryID 
                    FROM product 
                    WHERE ProductID =?"; 
            $stmt=$this->conn_db->prepare($sql);
            $stmt->execute([$id]);
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($row>0){ 
                return $row;
            } else {
                echo "<div class='feedback'><p>Error al obtener artículo.</p></div>";
            } 
        }

        public function listadoArticulos($login){

            $log = new Login();
            $sql= "SELECT ProductID, Name, Cost, Price, CategoryID FROM product";
            $stmt=$this->conn_db->query($sql);
    
            // Paginación.
            $limite_filas = 10;
            $total_paginas = ceil(($stmt->rowCount()) / $limite_filas);
     
            // Redirección de páginas y declaración de $pagina.
            if(isset($_GET["pagina"])){
                if($_GET["pagina"]==1){
                    header("Location:articulos.php");
                }else{
                    $pagina = $_GET["pagina"];
                }
            }else{
                $pagina = 1; 
            }
    
            // Calcular inicio de rango de páginas.
            $pagina_inicial = ($pagina-1) * $limite_filas;
    
            // Consulta sql con límite para paginación.
            $sql_limite = "SELECT ProductID, Name, Cost, Price, CategoryID FROM product LIMIT $pagina_inicial, $limite_filas";
            $stmt=$this->conn_db->query($sql_limite);
            
            if (($stmt->rowCount())> 0){
                echo "<table class=\"tabla\">\n
                        <tr>\n
                            <th>ID</th>\n
                            <th>Nombre</th>\n
                            <th>Coste</th>\n
                            <th>Precio</th>\n
                            <th>Categoría</th>\n";
    
                            if($log->compruebaSuperadmin($login)){
                                echo "<th>Manejo</th>\n
                                </tr>\n";
                            }
    
                // Mostrar filas.
                while($fila = $stmt->fetch(PDO::FETCH_ASSOC)){
                    echo "<tr>\n
                    <td>" . $fila["ProductID"] . "</td>\n
                    <td>" . $fila["Name"] . "</td>\n
                    <td>" . $fila["Cost"] . "</td>\n
                    <td>" . $fila["Price"] . "</td>\n
                    <td>";
                    echo $this->showNombreCategoria($fila["CategoryID"]) . "</td>";
    
                    if($log->compruebaSuperadmin($login)){
                        echo "<td><a href='formArticulos.php?editar=" . $fila["ProductID"] . "'><img src='img/editar.png'></a>
                        <a href='formArticulos.php?borrar=" . $fila["ProductID"] . "'><img src='img/borrar.png'></a>
                       </td>\n
                    </tr>";
                    } 
                };
                echo "</table>";
            }else{
                echo "<div class='feedback'><p>Error al obtener listado de artículos.</p></div>"; 
            }
     
            // Mostrar Paginación.
            echo "<div class='prev-next'>";
            
            if($pagina>1){
                echo "<a class='boton' href='?pagina=".($pagina-1)."'><<</a>";
            }else{
                echo "<a class='boton'><<</a>";
            }
            if($pagina<$total_paginas){
                 echo "<a class='boton' href='?pagina=".($pagina+1)."'>>></a>";
            }else{
                echo "<a class='boton'>>></a>";
            }
    
            echo "</div>";
        }

        // Get y show categorías.
        public function getListaCategorias(){

            $sql = "SELECT CategoryID, Name FROM category";
            $stmt=$this->conn_db->query($sql);
            
            if ($stmt){
                return $stmt;
            }else{
                return "Lista vacía";
            } 
        }
    
        // Mostrar nombre en lugar de id.
        public function showNombreCategoria($categoryID){

            $datos = $this->getListaCategorias();
            while($fila=$datos->fetch(PDO::FETCH_ASSOC)){
                if($fila["CategoryID"] == $categoryID){
                print_r($fila["Name"]);}
            } 
        }

        function showListaCategorias($categoryID){

            $datos = $this->getListaCategorias(); 

            if(is_string($datos)){  
                echo "<option value=''>" . $datos . "</option>";
            }else{
                while ($fila = $datos->fetch()){
                    // Si se viene de editar o borrar aparece la categoria por defecto.
                    if ($categoryID == $fila["CategoryID"]){
                        echo "<option selected='true' value='" . $fila["CategoryID"] . "'>" . $fila["Name"] . "</option>";
                    }else{
                        echo "<option  value='" . $fila["CategoryID"] . "'>" . $fila["Name"] . "</option>";
                    }
                }
            }
        }

        // Añadir, editar y borrar artículos.
        function crearArticulo($id, $nombre, $coste, $precio, $categoria){ 

            $sql = "INSERT INTO product (ProductID, Name, Cost, Price, CategoryID) 
                    VALUES (:productid, :name, :coste, :precio, :category)";

            $stmt=$this->conn_db->prepare($sql);
            $stmt->bindParam(':productid',$id, PDO::PARAM_INT);
            $stmt->bindParam(':name',$nombre);
            $stmt->bindParam(':coste',$coste);
            $stmt->bindParam(':precio',$precio);
            $stmt->bindParam(':category',$categoria);  
            $stmt->execute(); 
            $articulo=$stmt->rowCount(); 
    
            if($articulo>0){ 
                echo "<div class='feedback'><h2>Añadir artículo</h2><br><p>Se ha creado el artículo:<br> 
                    ID: " . $id . "<br> 
                    Nombre: " . $nombre . "<br>
                    Categoría: " . $categoria . "<br> 
                    Coste: " . $coste . "<br>
                    Precio: " . $precio . "</p><br><a class='boton' href='articulos.php'>Volver</a></div>";  
            
            }else{
                echo "<div class='feedback'><p>Error en la función crearArtículo.</p><br><a class='boton' href='articulos.php'>Volver</a></div>";
            }
            
        }

        function editarArticulo($id, $nombre, $coste, $precio, $categoria){
            
            $sql = "UPDATE product 
                    SET Name =:name,
                        Cost =:coste,
                        Price =:precio,
                        CategoryID =:category
                    WHERE ProductID =:productid";

            $stmt=$this->conn_db->prepare($sql);
            $stmt->bindParam(':productid',$id, PDO::PARAM_INT);
            $stmt->bindParam(':name',$nombre);
            $stmt->bindParam(':coste',$coste, PDO::PARAM_INT);
            $stmt->bindParam(':precio',$precio, PDO::PARAM_INT);
            $stmt->bindParam(':category',$categoria );  
            $stmt->execute();
            $articulo=$stmt->rowCount(); 
    
            if($articulo>0){ 
                echo "<div class='feedback'><h2>Editar artículo</h2><br><p>Se ha editado el artículo.</p><br><a class='boton' href='articulos.php'>Volver</a></div>"; 
            }else{
                echo "<div class='feedback'><p>Error en la función editarArtículo.</p><br><a class='boton' href='articulos.php'>Volver</a></div>";
            }
        }

        function borrarArticulo($id){ 

            $sql = "DELETE FROM product WHERE ProductID =?";
            $stmt=$this->conn_db->prepare($sql);
            $stmt->execute([$id]); 
            $articulo=$stmt->rowCount(); 
    
            if($articulo>0){ 
                echo "<div class='feedback'><h2>Borrar artículo</h2><br><p>Se ha borrado el artículo.</p><br><a class='boton' href='articulos.php'>Volver</a></div>";        
            }else{
                echo "<div class='feedback'><p>Error en la función borrarArtículo.</p><br><a class='boton' href='articulos.php'>Volver</a></div>";
            }
        } 
    }

    // Consultas para lista usuarios.
    class Users extends Connection{

        public function __construct(){
            parent::__construct();
        }

        public function listadoUsuarios(){

            $sql= "SELECT UserID, FullName, Email, LastAccess, Enabled FROM user";
            $stmt=$this->conn_db->query($sql);
            if($stmt->rowCount()>0){
                echo "<table class=\"tabla\">\n
                    <tr>\n
                        <th>ID</th>\n
                        <th>Nombre</th>\n
                        <th>Email</th>\n
                        <th>Último acceso</th>\n
                        <th>Autorizado</th>\n
                        <th>Manejo</th>\n
                    </tr>\n";
    
                while($fila=$stmt->fetch()){
                    echo "<tr class='admin'>\n
                            <td>" . $fila["UserID"] . "</td>\n
                            <td>" . $fila["FullName"] . "</td>\n
                            <td>" . $fila["Email"] . "</td>\n
                            <td>" . $fila["LastAccess"] . "</td>\n
                            <td>" . $fila["Enabled"] . "</td>\n
                            <td><a href='formUsuarios.php?editar=" . $fila["UserID"] . "'><img src='img/editar.png'></a>
                                <a href='formUsuarios.php?borrar=" . $fila["UserID"] . "'><img src='img/borrar.png'></a>
                                </td>\n
                        </tr>";
                    }
                echo "</table>";
    
                
            }else{
                echo "<div class='feedback'><p>Error al mostrar usuarios.</p></div>";
            }// Cerrar con? stmt?
        }
    
        public function getUsuarios($user_id){

            $sql = "SELECT UserID, FullName, Password, Email, LastAccess, Enabled 
                    FROM user 
                    WHERE UserID =?"; 
            $stmt=$this->conn_db->prepare($sql);
            $stmt->execute([$user_id]);
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($row>0){ 
                return $row;
            } else {
                echo "<div class='feedback'><p>Error al obtener usuario.</p></div>";
            } 
        }
    
        // Añadir, editar y borrar usuario.
        public function crearUsuario($id, $nombre, $pass, $email, $acceso, $enabled){

            $password = password_hash($pass, PASSWORD_BCRYPT);
    
            $sql = "INSERT INTO user (UserID, FullName, Password, Email, LastAccess, Enabled) VALUES (:userid, :fname, :pass, :email, :access, :enabled)";
            $stmt=$this->conn_db->prepare($sql);
            $stmt->bindParam(':userid',$id, PDO::PARAM_INT);
            $stmt->bindParam(':fname',$nombre);
            $stmt->bindParam(':pass',$password);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':access',$acceso);
            $stmt->bindParam(':enabled',$enabled);  
            $stmt->execute(); 
            $user=$stmt->rowCount();
    
            if($user>0){
                echo "<div class='feedback'><h2>Añadir usuario</h2><br><p>Se ha creado el usuario:<br> 
                        ID: " . $id . "<br> 
                        Nombre: " . $nombre . "<br>
                        Contraseña: " . $pass . "<br>
                        Correo: " . $email . "<br>
                        Último Acceso: " . $acceso . "<br> 
                        Autorizado: " . $enabled . "</p><br><a class='boton' href='usuarios.php'>Volver</a></div>";    
    
             
            }else{
                echo "<div class='feedback'><p>Error en la función crearUsuario.</p><br><a class='boton' href='usuarios.php'>Volver</a></div>"; 
            }
            
        }
          
        public function editarUsuario($id, $nombre, $pass, $email, $acceso, $enabled){ 

            $sql = "UPDATE user 
                    SET UserID =:userid,
                        FullName =:fname,
                        Password =:pass,
                        Email =:email,
                        LastAccess =:access,
                        Enabled =:enabled
                    WHERE UserID =:userid";
            
            $stmt=$this->conn_db->prepare($sql);
            
            $stmt->bindParam(':userid',$id, PDO::PARAM_INT);
            $stmt->bindParam(':fname',$nombre);
            $stmt->bindParam(':pass',$pass);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':access',$acceso);
            $stmt->bindParam(':enabled',$enabled);  
            $stmt->execute();
            $user=$stmt->rowCount();
    
            if($user>0){
                echo "<div class='feedback'><h2>Editar usuario</h2><br><p>Se ha editado el usuario.</p><br><a class='boton' href='usuarios.php'>Volver</a></div>";
                       
            }else{
                echo "<div class='feedback'><p>Error en la función editarUsuario.</p><br><a class='boton' href='usuarios.php'>Volver</a></div>"; 
            }
        }
    
        public function borrarUsuario($id){

            $sql = "DELETE FROM user WHERE UserID =?";
            $stmt=$this->conn_db->prepare($sql);
            $stmt->execute([$id]);
            $user=$stmt->rowCount(); 
     
            if($user>0){
                echo "<div class='feedback'><h2>Borrar usuario</h2><br><p>Se ha borrado el usuario.</p><br><a class='boton' href='usuarios.php'>Volver</a></div>"; 
            }else{
                echo "<div class='feedback'><p>Error en la función borrarUsuario.</p><br><a class='boton' href='usuarios.php'>Volver</a></div>";
            }
        }  
    }

?>