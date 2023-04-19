<?php 

namespace Controllers;
use MVC\Router;
use Model\Propiedad;
use Model\Vendedor;
use Intervention\Image\ImageManagerStatic as Image;




class PropiedadControllers {
    // PARA NO PERDER LA REFERENCIA DE LO QUE TENEMOS EN LA INSTANCIA DEL ROUTER
    // EN NUESTRO METODO LE PASAMOS COMO PAREMETRO LA CLASE Y LA VARIABLE QUE TIENE 
    // ASOCIADA LA INSTANCIA QUE NOSOTROS NECESITAMOS
    public static function index(Router $router){
        $propiedades = Propiedad::all();
        $vendedores = Vendedor::all();

        $resultado = $_GET['resultado'] ?? null;
        $router->render('propiedades/admin', [
            'propiedades' => $propiedades,
            'vendedores' => $vendedores,

            'resultado' => $resultado
        ]);
    }

    public static function crear(Router $router){
        $propiedad = new Propiedad;
        $vendedores = Vendedor::all();
        $errores = Propiedad::getErrores();


        if($_SERVER["REQUEST_METHOD"] === "POST"){
           
            // CREA UNA NUEVA INSTANCIA
        $propiedad = new Propiedad($_POST['propiedad']);
        
        // **---SUBIDA DE ARCHIVOS----**
        

        // GENERAR UN NOMBRE UNICO
        $nombreImagen = md5(uniqid(rand(), true)).".jpg";
        
        
        // SETEA LA IMAGEN
        // REALIZA UN RESIZE A LA IMAGE CON INTERVETION
        if($_FILES['propiedad']['tmp_name']['imagen']){
            $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800,600);
            $propiedad->setImagen($nombreImagen);
        }

        // VALIDAR
        $errores = $propiedad->validar();

       
        // REVISAR QUE EL ARREGLO DE ERRORES ESTE VACIO
        if(empty($errores)){
           
            // CREAR CARPETA
            if(!is_dir(CARPETA_IMAGENES)){
                mkdir(CARPETA_IMAGENES);
            }
            // GUARDA LA IMAGEN EN EL SERVIDOR
            $image->save(CARPETA_IMAGENES . $nombreImagen);
            
         

            // GUARDA EB LA DB
           $propiedad->guardar();

            
        }
        }

        $router->render('propiedades/crear', [
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);
    }



    public static function actualizar(Router $router){
        $id = validarORedireccionar('/admin');
        $propiedad = Propiedad::find($id);
        $vendedores = Vendedor::all();


        $errores = Propiedad::getErrores();

        if($_SERVER["REQUEST_METHOD"] === "POST"){
        
            // ASIGNAR LOS ATRIBUTOS
            $args = $_POST['propiedad'];
            $propiedad->sincronizar($args);
            
            // VALIDACION
            $errores = $propiedad->validar();
            
            // SUBIDA DE ARCHIVOS
            // GENERAR UN NOMBRE UNICO
            $nombreImagen = md5(uniqid(rand(), true)).".jpg";
            
            
            // SETEA LA IMAGEN
            // REALIZA UN RESIZE A LA IMAGE CON INTERVETION
            if($_FILES['propiedad']['tmp_name']['imagen']){
                $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800,600);
                $propiedad->setImagen($nombreImagen);
            }
    
            if(empty($errores)){
                if($_FILES['propiedad']['tmp_name']['imagen']){
                    // ALMACENAR LA IMAGEB
                    $image->save(CARPETA_IMAGENES . $nombreImagen);
                }
                
                
                // ACTUALIZAR EN LA DB
                  $resultado = $propiedad->guardar();
    
                    
            }
        }

        $router->render('propiedades/actualizar', [
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores

        ]);
    }


    public static function eliminar(){
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $id = $_POST['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if($id){
            $tipo = $_POST['tipo'];

            if(validarTipoContenido($tipo)){
                
                $propiedad = Propiedad::find($id);
                $propiedad->eliminar();
            }
            
          
        }
        }
    }
}