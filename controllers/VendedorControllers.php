<?php 

namespace Controllers;
use MVC\Router;
use Model\Vendedor;
use Intervention\Image\ImageManagerStatic as Image;




class VendedorControllers {
    // PARA NO PERDER LA REFERENCIA DE LO QUE TENEMOS EN LA INSTANCIA DEL ROUTER
    // EN NUESTRO METODO LE PASAMOS COMO PAREMETRO LA CLASE Y LA VARIABLE QUE TIENE 
    // ASOCIADA LA INSTANCIA QUE NOSOTROS NECESITAMOS
    

    public static function crear(Router $router){
        $errores = Vendedor::getErrores();
        
        $vendedor =  new Vendedor();


        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $vendedor = new Vendedor($_POST['vendedor']);
            
            $errores  = $vendedor->validar();
            if(empty($errores)){
               
                // GUARDA EB LA DB
               $vendedor->guardar();
    
                
            }
        }
        

        $router->render('vendedores/crear', [
            
            'vendedor' => $vendedor,
            'errores' => $errores
        ]);
    }



    public static function actualizar(Router $router){
        $errores = Vendedor::getErrores();

        $id = validarORedireccionar('/admin');

        // OBTENER DATOS DEL VENDEDOR A ACTUALIZAR
        $vendedor = Vendedor::find($id);
 

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            // ASIGNAR LOS VALORES 
            $args = $_POST['vendedor'];
    
            
            // SINCRONIZAR OBJETO EN MEMORIA CON LO QUE EL USUARIO ESCRIBIO
            $vendedor->sincronizar($args);
            
            $errores = $vendedor->validar();
            if(empty($errores)){
               
                // GUARDA EB LA DB
               $vendedor->guardar();
    
                
            }
        }

        $router->render('vendedores/actualizar', [
            
            'vendedor' => $vendedor,
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
                    $vendedor = Vendedor::find($id);
                    $vendedor->eliminar();
                }

            }
        }
    }
}